<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\Inc\AssessmentQuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\Inc\ScoreCalculator;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use HTML_QuickForm_Action;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * $Id: assessment_viewer_wizard_process.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard
 */
class AssessmentViewerWizardProcess extends HTML_QuickForm_Action
{

    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function perform($page, $actionName)
    {
        $html = array();
        $html[] = $this->parent->get_parent()->render_header();

        $html[] = '<div class="assessment">';
        $html[] = '<h2>' . Translation :: get('ResultsFor') . ': ' . $this->parent->get_assessment()->get_title() .
             '</h2>';
        $html[] = '</div>';

        $values = $this->parent->exportValues();

        foreach ($values as $key => $value)
        {
            $value = Security :: remove_XSS($value);
            $split_key = split('_', $key);
            $question_id = $split_key[0];

            if (is_numeric($question_id))
            {
                $answer_index = $split_key[1];
                $values[$question_id][$answer_index] = $value;
            }
        }

        // $question_numbers = $_SESSION['questions'];

        $assessment = $this->parent->get_assessment();
        if ($assessment->get_random_questions() == 0)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($assessment->get_id()),
                ComplexContentObjectItem :: get_table_name());
        }
        else
        {
            $condition = new InCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_ID),
                $_SESSION['questions'],
                ComplexContentObjectItem :: get_table_name());
        }

        $questions_cloi = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            $condition);

        $question_number = 1;
        $total_score = 0;
        $total_weight = 0;

        while ($question_cloi = $questions_cloi->next_result())
        {
            $question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $question_cloi->get_ref());
            $answers = $values[$question_cloi->get_id()];
            $question_cloi->set_ref($question);

            $score_calculator = ScoreCalculator :: factory($question, $answers, $question_cloi->get_weight());
            $score = $score_calculator->calculate_score();
            $total_score += $score;
            $total_weight += $question_cloi->get_weight();

            $question_number ++;

            $this->parent->get_parent()->save_assessment_answer(
                $question_cloi->get_id(),
                serialize($answers),
                $score,
                $values['hint_question'][$question_cloi->get_id()]);
        }

        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel" style="float: left;">';
        $html[] = Translation :: get('TotalScore');
        $html[] = '</div>';
        $html[] = '<div class="bevel" style="text-align: right;">';

        if ($total_score < 0)
            $total_score = 0;

        $percent = round(($total_score / $total_weight) * 100);

        $html[] = $total_score . ' / ' . $total_weight . ' (' . $percent . '%)';
        $html[] = '</div>';

        $html[] = '</div></div></div>';
        $html[] = '<div class="clear"></div>';

        $questions_cloi = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            $condition);

        $question_number = 1;

        while ($question_cloi = $questions_cloi->next_result())
        {
            $question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $question_cloi->get_ref());
            $answers = $values[$question_cloi->get_id()];
            $hints = $values['hint_question'][$question_cloi->get_id()];
            $question_cloi->set_ref($question);

            $score_calculator = ScoreCalculator :: factory($question, $answers, $question_cloi->get_weight());
            $score = $score_calculator->calculate_score();

            $display = AssessmentQuestionResultDisplay :: factory(
                $question_cloi,
                $question_number,
                $answers,
                $score,
                $hints);
            $html[] = $display->as_html();

            $question_number ++;
        }

        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel" style="float: left;">';
        $html[] = Translation :: get('TotalScore');
        $html[] = '</div>';
        $html[] = '<div class="bevel" style="text-align: right;">';

        if ($total_score < 0)
            $total_score = 0;

        $percent = round(($total_score / $total_weight) * 100);

        $html[] = $total_score . ' / ' . $total_weight . ' (' . $percent . '%)';
        $html[] = '</div>';

        $html[] = '</div></div></div>';
        $html[] = '<div class="clear"></div>';

        $this->parent->get_parent()->save_assessment_result($percent);

        unset($_SESSION['questions']);

        $back_url = $this->parent->get_parent()->get_assessment_go_back_url();

        if ($back_url)
        {
            $html[] = '<a href="' . $back_url . '">' . Translation :: get('GoBack') . '</a>';
        }

        $html[] = $this->parent->get_parent()->render_footer();

        return implode(PHP_EOL, $html);
    }
}
