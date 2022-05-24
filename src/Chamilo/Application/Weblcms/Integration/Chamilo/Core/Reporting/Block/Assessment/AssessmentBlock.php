<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Iterator\DataClassCollection;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Abstract class that defines common functionality for blocks of the assessment
 *
 * @package application\weblcms\integration\core\reporting
 */
abstract class AssessmentBlock extends ToolBlock
{
    const PROPERTY_ASSESSMENT_ATTEMPT = 'assessment_attempt';

    /**
     * Adds a category to the reporting data from the given array data
     *
     * @param string $category_name
     * @param mixed[] $array
     * @param \Chamilo\Core\Reporting\ReportingData $reporting_data
     */
    protected function add_category_from_array($category_name, $array, $reporting_data)
    {
        foreach ($array as $header => $value)
        {
            $reporting_data->add_data_category_row($header, $category_name, $value);
        }
    }

    /**
     * Adds a row to the reporting data from the given array data
     *
     * @param int $row_count
     * @param mixed[] $array
     * @param \Chamilo\Core\Reporting\ReportingData $reporting_data
     */
    protected function add_row_from_array($row_count, $array, $reporting_data)
    {
        foreach ($array as $header => $value)
        {
            $reporting_data->add_data_category_row($row_count, $header, $value);
        }
    }

    /**
     * Returns the assessment attempts data by a given publication id, optionally limited by user
     *
     * @param int $publication_id
     * @param int $user_id
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection<\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt>
     */
    protected function get_assessment_attempts($publication_id, $user_id = null)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($publication_id)
        );

        if ($user_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id)
            );
        }

        $condition = new AndCondition($conditions);

        return WeblcmsTrackingDataManager::retrieves(
            AssessmentAttempt::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Returns the assessment information for a given publication
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $assessment_publication
     *
     * @return string[string]
     */
    protected function get_assessment_information($assessment_publication)
    {
        $assessment_information = [];

        $assessment = $assessment_publication->get_content_object();

        $assessment_information[Translation::get('AssessmentTitle')] = $this->get_assessment_title_with_link(
            $assessment_publication
        );

        $assessment_information[Translation::get('AssessmentDescription')] = $assessment->get_description();

        $assessment_information[Translation::get('Published')] = DatetimeUtilities::format_locale_date(
            null, $assessment_publication->get_publication_date()
        );

        $assessment_information[Translation::get('LastModified')] = DatetimeUtilities::format_locale_date(
            null, $assessment_publication->get_modified_date()
        );

        return $assessment_information;
    }

    /**
     * Returns the assessment information headers
     *
     * @return string[]
     */
    protected function get_assessment_information_headers()
    {
        return array(
            Translation::get('AssessmentTitle'),
            Translation::get('AssessmentDescription'),
            Translation::get('Published'),
            Translation::get('LastModified')
        );
    }

    /**
     * Returns the link to the assessment result viewer for an assessment or an individual question
     *
     * @param int $assessment_attempt_id
     * @param int $question_id
     *
     * @return string
     */
    protected function get_assessment_result_viewer_link($assessment_attempt_id, $question_id = null)
    {
        $glyph = new FontAwesomeGlyph('info-circle');

        $params = [];

        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $this->getCourseId();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] =
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                Assessment::package()
            );
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = Manager::ACTION_ATTEMPT_RESULT_VIEWER;
        $params[Manager::PARAM_USER_ASSESSMENT] = $assessment_attempt_id;

        if ($question_id)
        {
            $params[Manager::PARAM_QUESTION_ATTEMPT] = $question_id;
        }

        $params[Manager::PARAM_ASSESSMENT] = $this->getPublicationId();

        $redirect = new Redirect($params);
        $link = $redirect->getUrl();

        return '<a href="' . $link . '">' . $glyph->render() . '</a>';
    }

    /**
     * Returns the assessment title with a link
     *
     * @param $assessment_publication
     *
     * @return string
     */
    protected function get_assessment_title_with_link($assessment_publication)
    {
        $assessment = $assessment_publication->get_content_object();

        $params = [];
        $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $assessment_publication->get_course_id();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $assessment_publication->get_tool();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $assessment_publication->get_id();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;

        $redirect = new Redirect($params);
        $url = $redirect->getUrl();

        return '<a href="' . $url . '">' . $assessment->get_title() . '</a>';
    }

    /**
     * Returns the assessment question attempts data by a question complex content object item id and/or a given set of
     * assessment attempt id's
     *
     * @param int $question_cid
     * @param int[] $assessment_attempt_ids
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection<\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt>
     */
    protected function get_question_attempts($question_cid = null, $assessment_attempt_ids = [])
    {
        $conditions = [];

        if ($question_cid)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    QuestionAttempt::class, QuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID
                ), new StaticConditionVariable($question_cid)
            );
        }

        if ($assessment_attempt_ids)
        {
            if (!is_array($assessment_attempt_ids))
            {
                $assessment_attempt_ids = array($assessment_attempt_ids);
            }

            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    QuestionAttempt::class, QuestionAttempt::PROPERTY_ASSESSMENT_ATTEMPT_ID
                ), $assessment_attempt_ids
            );
        }

        $condition = new AndCondition($conditions);

        return WeblcmsTrackingDataManager::retrieves(
            QuestionAttempt::class, new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * Returns the question attempts from a given publication and question (optionally limited by user)
     *
     * @param int $publication_id
     * @param int $question_cid
     * @param int $user_id
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    protected function get_question_attempts_from_publication_and_question(
        $publication_id, $question_cid, $user_id = null
    )
    {
        $assessment_attempt_ids = [];
        $assessment_attempts_by_id = [];

        $assessment_attempts = $this->get_assessment_attempts($publication_id, $user_id);
        foreach ($assessment_attempts as $assessment_attempt)
        {
            $assessment_attempt_ids[] = $assessment_attempt->get_id();
            $assessment_attempts_by_id[$assessment_attempt->get_id()] = $assessment_attempt;
        }

        if (count($assessment_attempt_ids) == 0)
        {
            return new DataClassCollection(AssessmentAttempt::class, []);
        }

        $question_attempts = $this->get_question_attempts($question_cid, $assessment_attempt_ids);

        foreach ($question_attempts as $question_attempt)
        {
            $question_attempt->setOptionalProperty(
                self::PROPERTY_ASSESSMENT_ATTEMPT,
                $assessment_attempts_by_id[$question_attempt->get_assessment_attempt_id()]
            );
        }

        $question_attempts->rewind();

        return $question_attempts;
    }

    /**
     * Returns the selected question id from the url
     *
     * @return int
     */
    protected function get_question_id()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_QUESTION);
    }

    /**
     * Returns the assessment information for a given publication
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $question
     *
     * @return string[string]
     */
    protected function get_question_information($question)
    {
        $question_information = [];

        $question_information[Translation::get('QuestionTitle')] = $question->get_title();
        $question_information[Translation::get('QuestionDescription')] = $question->get_description();
        $question_information[Translation::get('QuestionType')] = Translation::get(
            'TypeName', [], ClassnameUtilities::getInstance()->getNamespaceFromClassname($question->getType())
        );

        return $question_information;
    }

    /**
     * Returns the question information headers
     *
     * @return string[]
     */
    protected function get_question_information_headers()
    {
        return array(
            Translation::get('QuestionTitle'),
            Translation::get('QuestionDescription'),
            Translation::get('QuestionType')
        );
    }

    /**
     * Renders the difficulty index
     *
     * @param int $correct_answers
     * @param int $total_answers
     *
     * @return string
     */
    protected function render_difficulty_index($correct_answers, $total_answers)
    {
        $difficulty_index = round($correct_answers / $total_answers, 2);

        return $this->get_score_bar($difficulty_index * 100);
        /*
         * $color = $difficulty_index > 0.75 ? 'orange' : ($difficulty_index < 0.25 ? 'red' : 'green'); return '<span
         * style="color: ' . $color . '; font-weight: bold;">' . $difficulty_index . '</span>';
         */
    }
}