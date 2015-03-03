<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
class AssessmentMatchTextQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $best_option = $this->get_question()->get_best_option();
        $best_answer = $this->get_score() == $best_option->get_score();
        $feedback_answer = ($this->get_complex_content_object_question()->get_feedback_answer() && ! $best_answer) ||
             ! $this->get_complex_content_object_question()->get_feedback_answer();
        $valid_answer = $this->get_score() > 0;
        $user_answer = $this->get_answers();
        $answer_option = $this->get_question()->get_option(
            $user_answer[0],
            $this->get_question()->get_ignore_case(),
            $this->get_question()->get_use_wildcards());

        $html = array();

        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th style="width: 50%;">' . Translation :: get('UserAnswer') . '</th>';
        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && $feedback_answer &&
             ! $this->can_change())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $html[] = '<tr class="row_even">';

        if (! is_null($user_answer[0]) && $user_answer[0] != '')
        {
            if ($valid_answer && $best_option->matches(
                $user_answer[0],
                $this->get_question()->get_ignore_case(),
                $this->get_question()->get_use_wildcards()))
            {
                $result = ' <img style="vertical-align: middle;" src="' . Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                    'AnswerCorrect') . '" alt="' . Translation :: get('Correct') . '" title="' .
                     Translation :: get('Correct') . '" style="" />';
            }
            elseif ($valid_answer)
            {
                $result = ' <img style="vertical-align: middle;" src="' . Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                    'AnswerWarning') . '" alt="' . Translation :: get('CorrectButNotBest') . '" title="' .
                     Translation :: get('CorrectButNotBest') . '" style="" />';
            }
            else
            {
                $result = ' <img style="vertical-align: middle;" src="' .
                     Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                        'AnswerWrong') . '" alt="' . Translation :: get('Wrong') . '" title="' .
                     Translation :: get('Wrong') . '" />';
            }

            $html[] = '<td>' . $user_answer[0] . $result . '</td>';
        }
        else
        {
            $result = ' <img style="vertical-align: middle;" src="' .
                 Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                    'AnswerWrong') . '" alt="' . Translation :: get('Wrong') . '" title="' . Translation :: get('Wrong') .
                 '" />';
            $html[] = '<td>' . Translation :: get('NoAnswer') . $result . '</td>';
        }

        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && $feedback_answer &&
             ! $this->can_change())
        {
            if (! is_null($answer_option))
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->get_results_viewer(),
                    $answer_option->get_feedback());
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }
            else
            {
                $html[] = '<td>-</td>';
            }
        }

        $html[] = '</tr>';

        $html[] = '</tbody>';
        $html[] = '</table>';

        if ($feedback_answer && (! $valid_answer || ($valid_answer && ! $best_option->matches(
            $user_answer[0],
            $this->get_question()->get_ignore_case(),
            $this->get_question()->get_use_wildcards()))))
        {
            $html[] = '<table class="data_table take_assessment">';
            $html[] = '<thead>';
            $html[] = '<tr>';
            $html[] = '<th style="width: 50%;">' . Translation :: get('BestPossibleAnswer') . '</th>';

            if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
            {
                $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
            }

            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';

            $html[] = '<tr class="row_even">';
            $html[] = '<td>' . $best_option->get_value() . '</td>';

            if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->get_results_viewer(),
                    $best_option->get_feedback());
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }

            $html[] = '</tr>';
            $html[] = '</tbody>';
            $html[] = '</table>';
        }

        return implode(PHP_EOL, $html);
    }
}
