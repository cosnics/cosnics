<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssessmentQuestionUsersTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of the scores of each question in
 *          the assessment
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssessmentQuestionsBlock extends AssessmentBlock
{

    /**
     *
     * @var MetadataFilterForm
     */
    private $form;

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $rows = array(Translation :: get('QuestionTitle'), Translation :: get('QuestionType'));

        $rows = array_merge($rows, $this->get_question_reporting_info_headers());
        $rows[] = Translation :: get('QuestionDetails');

        $reporting_data->set_rows($rows);

        $questions = $this->get_questions();

        $img = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Reporting') . '" title="' .
             Translation :: get('Details') . '" />';

        $count = 0;

        // while ($question = $questions->next_result())
        foreach ($questions as $question)
        {
            $count ++;

            $reporting_data->add_category($count);

            $reporting_info = $this->get_question_reporting_info($question);
            $this->add_row_from_array($count, $reporting_info, $reporting_data);

            $attempt_count = $reporting_info[Translation :: get('NumberOfAttempts')];
            if ($attempt_count > 0)
            {
                $params = $this->get_parent()->get_parameters();
                $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TEMPLATE_ID] = AssessmentQuestionUsersTemplate :: class_name();
                $params[\Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager :: PARAM_QUESTION] = $question->get_id();
                $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $img . '</a>';
                $reporting_data->add_data_category_row($count, Translation :: get('QuestionDetails'), $link);
            }
        }

        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }

    /**
     * Returns the question reporting headers
     *
     * @return string[]
     */
    protected function get_question_reporting_info_headers()
    {
        return array(
            Translation :: get('NumberOfAttempts'),
            Translation :: get('AverageScore'),
            Translation :: get('MinScoreAchieved'),
            Translation :: get('MaxScoreAchieved'),
            Translation :: get('DifficultyIndex'));
    }

    /**
     * Returns the question reporting info for a given question
     *
     * @param ComplexContentObjectItem $complex_question
     *
     * @return mixed[]
     */
    protected function get_question_reporting_info($complex_question)
    {
        $weight = $complex_question->get_weight();

        $question_attempts = $this->get_question_attempts_from_publication_and_question(
            $this->get_publication_id(),
            $complex_question->get_id());

        $score = $min = $max = null;
        $correct_answers = $finished_attempts = 0;

        while ($question_attempt = $question_attempts->next_result())
        {
            $assessment_attempt = $question_attempt->get_optional_property(self :: PROPERTY_ASSESSMENT_ATTEMPT);

            if ($assessment_attempt->get_status() == AssessmentAttempt :: STATUS_COMPLETED)
            {
                if ($question_attempt->get_score() == $weight)
                {
                    $correct_answers ++;
                }

                if (is_null($min) || $min > $question_attempt->get_score())
                {
                    $min = $question_attempt->get_score();
                }
                if (is_null($max) || $max < $question_attempt->get_score())
                {
                    $max = $question_attempt->get_score();
                }

                $score += $question_attempt->get_score();

                $finished_attempts ++;
            }
        }

        if ($finished_attempts > 0)
        {
            $score = $this->get_score_bar($score / $finished_attempts / $complex_question->get_weight() * 100);
            $difficulty_index = $this->render_difficulty_index($correct_answers, $finished_attempts);
        }

        if (! is_null($min))
        {
            $min = $this->get_score_bar($min / $weight * 100);
        }

        if (! is_null($max))
        {
            $max = $this->get_score_bar($max / $weight * 100);
        }

        $object = $complex_question->get_ref_object();

        $type = Translation :: get(
            'TypeName',
            array(),
            ClassnameUtilities :: getInstance()->getNamespaceFromClassname($object->get_type()));

        $reporting_info = array();

        $reporting_info[Translation :: get('QuestionTitle')] = $object->get_title();
        $reporting_info[Translation :: get('QuestionType')] = $type;
        $reporting_info[Translation :: get('NumberOfAttempts')] = $question_attempts->size();
        $reporting_info[Translation :: get('DifficultyIndex')] = $difficulty_index;
        $reporting_info[Translation :: get('AverageScore')] = $score;
        $reporting_info[Translation :: get('MinScoreAchieved')] = $min;
        $reporting_info[Translation :: get('MaxScoreAchieved')] = $max;

        return $reporting_info;
    }

    private function get_questions()
    {
        $pid = $this->get_publication_id();

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $pid);

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(),
                ComplexContentObjectItem :: PROPERTY_PARENT),
            new StaticConditionVariable($publication->get_content_object_id()));

        $questions = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            new DataClassRetrievesParameters($condition));

        $questions_arr = array();
        while ($question = $questions->next_result())
        {
            $questions_arr[] = $question;
        }

        return $questions_arr;
    }
}
