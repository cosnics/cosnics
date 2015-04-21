<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_rating_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class AssessmentRatingQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $answers = $this->get_answers();
        $correct_answer = $answers[0] == $this->get_question()->get_correct();
        $feedback_answer = ($this->get_complex_content_object_question()->get_feedback_answer() && ! $correct_answer) ||
             ! $this->get_complex_content_object_question()->get_feedback_answer();

        $html = array();
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list">' . Translation :: get('UserRating') . '</th>';
        $html[] = '<th class="list">' . Translation :: get('CorrectRating') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $html[] = '<tr>';

        if ($correct_answer)
        {
            $result = ' <img style="vertical-align: middle;" src="' . Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                'AnswerCorrect') . '" alt="' . Translation :: get('Correct') . '" title="' . Translation :: get(
                'Correct') . '" style="" />';
        }
        else
        {
            $result = ' <img style="vertical-align: middle;" src="' .
                 Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                    'AnswerWrong') . '" alt="' . Translation :: get('Wrong') . '" title="' . Translation :: get('Wrong') .
                 '" />';
        }

        $html[] = '<td>' . $answers[0] . $result . '</td>';
        $html[] = '<td>' . $this->get_question()->get_correct() . '</td>';
        $html[] = '</tr>';

        $html[] = '</tbody>';
        $html[] = '</table>';

        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && $feedback_answer &&
             ! $this->can_change())
        {
            $object_renderer = new ContentObjectResourceRenderer(
                $this->get_results_viewer(),
                $this->get_question()->get_feedback());

            $html[] = '<div class="splitter">';
            $html[] = Translation :: get('Feedback');
            $html[] = '</div>';

            $html[] = '<div style="padding: 10px; border-left: 1px solid #B5CAE7; border-right: 1px solid #B5CAE7; border-bottom: 1px solid #B5CAE7;">';
            $html[] = $object_renderer->run();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
