<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssessmentAttemptsTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying all assessments within a course and
 *          their attempt stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentsBlock extends AssessmentBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $rows = array(Translation::get('Title'));

        $rows = array_merge($rows, $this->get_assessment_reporting_info_headers());
        $rows[] = Translation::get('AssessmentDetails');

        $reporting_data->set_rows($rows);

        $course_id = $this->getCourseId();
        $tool = ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assessment::class_name(), true);

        $count = 1;
        $glyph = new FontAwesomeGlyph('pie-chart');

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable($tool)
        );
        $condition = new AndCondition($conditions);

        $pub_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            ContentObjectPublication::class_name(), new DataClassRetrievesParameters($condition)
        );

        while ($pub = $pub_resultset->next_result())
        {
            $params = $this->get_parent()->get_parameters();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = AssessmentAttemptsTemplate::class_name();
            $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $pub->get_id();
            $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $glyph->render() . '</a>';

            $reporting_data->add_category($count);

            $reporting_data->add_data_category_row(
                $count, Translation::get('Title'), $this->get_assessment_title_with_link($pub)
            );

            $assessment_reporting_info = $this->get_assessment_reporting_info($pub);
            foreach ($assessment_reporting_info as $translation => $value)
            {
                $reporting_data->add_data_category_row($count, $translation, $value);
            }

            $reporting_data->add_data_category_row($count, Translation::get('AssessmentDetails'), $link);

            $count ++;
        }
        $reporting_data->hide_categories();

        return $reporting_data;
    }

    /**
     * Returns the headers for the assessment reporting info
     *
     * @return array
     */
    protected function get_assessment_reporting_info_headers()
    {
        return array(
            Translation::get('ResponseUsers'), Translation::get('NumberOfAttempts'), Translation::get('FirstAttempt'),
            Translation::get('LastAttempt'), Translation::get('AverageScore'), Translation::get('MinScoreAchieved'),
            Translation::get('MaxScoreAchieved')
        );
    }

    /**
     * Returns the assessment reporting info for a given publication
     *
     * @param $assessment_publication
     *
     * @return array
     */
    protected function get_assessment_reporting_info($assessment_publication)
    {
        $reporting_info = array();

        $target_users = WeblcmsDataManager::get_publication_target_users($assessment_publication);
        $assessment_attempts = $this->get_assessment_attempts($assessment_publication->get_id());

        $score_count = 0;
        $min_score = $max_score = $last_attempt = $first_attempt = $score = null;

        $user_ids = array();

        while ($assessment_attempt = $assessment_attempts->next_result())
        {
            if ($assessment_attempt->get_start_time() > $last_attempt)
            {
                $last_attempt = $assessment_attempt->get_start_time();
            }

            if ($assessment_attempt->get_status() == AssessmentAttempt::STATUS_COMPLETED)
            {
                $score += $assessment_attempt->get_total_score();
                $score_count ++;

                if (is_null($min_score) || $assessment_attempt->get_total_score() < $min_score)
                {
                    $min_score = $assessment_attempt->get_total_score();
                }

                if (is_null($max_score) || $assessment_attempt->get_total_score() > $max_score)
                {
                    $max_score = $assessment_attempt->get_total_score();
                }

                $user_ids[] = $assessment_attempt->get_user_id();
            }

            if (is_null($first_attempt) || $assessment_attempt->get_start_time() < $first_attempt)
            {
                $first_attempt = $assessment_attempt->get_start_time();
            }
        }

        if (!is_null($last_attempt))
        {
            $last_attempt = DatetimeUtilities::format_locale_date(null, $last_attempt);
        }

        if (!is_null($first_attempt))
        {
            $first_attempt = DatetimeUtilities::format_locale_date(null, $first_attempt);
        }

        if (!is_null($score))
        {
            $score = $this->get_score_bar($score / $score_count);
        }

        if (!is_null($min_score))
        {
            $min_score = $this->get_score_bar($min_score);
        }

        if (!is_null($max_score))
        {
            $max_score = $this->get_score_bar($max_score);
        }

        $reporting_info[Translation::get('NumberOfAttempts')] = $assessment_attempts->size();

        $reporting_info[Translation::get('ResponseUsers')] =
            count(array_unique($user_ids)) . ' / ' . count($target_users);

        $reporting_info[Translation::get('FirstAttempt')] = $first_attempt;
        $reporting_info[Translation::get('LastAttempt')] = $last_attempt;
        $reporting_info[Translation::get('AverageScore')] = $score;
        $reporting_info[Translation::get('MinScoreAchieved')] = $min_score;
        $reporting_info[Translation::get('MaxScoreAchieved')] = $max_score;

        return $reporting_info;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
