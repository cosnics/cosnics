<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;

/**
 * $Id: assessment_matching_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class AssessmentMatchingQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $labels = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z');

        $configuration = $this->get_results_viewer()->get_configuration();

        $html = array();
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th>' . Translation :: get('PossibleMatches') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $label = 'A';
        $matches = $this->get_question()->get_matches();
        foreach ($matches as $i => $match)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $label . '.</td>';

            if ($this->get_question()->get_display() == AssessmentMatchingQuestion :: DISPLAY_LIST)
            {

                $object_renderer = new ContentObjectResourceRenderer($this->get_results_viewer(), $match);
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }
            else
            {
                $html[] = '<td>' . strip_tags($match) . '</td>';
            }
            $html[] = '</tr>';
            $label ++;
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th>' . Translation :: get('Option') . '</th>';
        $html[] = '<th>' . Translation :: get('YourMatch') . '</th>';

        if ($configuration->show_solution())
        {
            $html[] = '<th>' . Translation :: get('Correct') . '</th>';
        }

        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $answers = $this->get_answers();

        $options = $this->get_question()->get_options();
        foreach ($options as $i => $option)
        {
            $valid_answer = $answers[$i] == $option->get_match();

            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';

            $label = $i + 1;
            $html[] = '<td>' . $label . '. </td>';

            $object_renderer = new ContentObjectResourceRenderer($this->get_results_viewer(), $option->get_value());
            $html[] = '<td>' . $object_renderer->run() . '</td>';

            if ($configuration->show_correction() || $configuration->show_solution())
            {
                if ($valid_answer)
                {
                    $result = ' <img src="' . Theme :: getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') .
                         '" alt="' . Translation :: get('Correct') . '" title="' . Translation :: get('Correct') .
                         '" style="" />';
                }
                else
                {
                    $result = ' <img src="' . Theme :: getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') .
                         '" alt="' . Translation :: get('Wrong') . '" title="' . Translation :: get('Wrong') . '" />';
                }
            }
            else
            {
                $result = '';
            }

            if ($answers[$i] == - 1)
            {
                $html[] = '<td>' . Translation :: get('NoAnswer') . $result . '</td>';
            }
            else
            {
                $html[] = '<td>' . $labels[$answers[$i]] . $result . '</td>';
            }

            if ($configuration->show_solution())
            {
                $html[] = '<td>' . $labels[$option->get_match()] . '</td>';
            }

            if (AnswerFeedbackDisplay :: allowed(
                $configuration,
                $this->get_complex_content_object_question(),
                true,
                $valid_answer))
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->get_results_viewer(),
                    $option->get_feedback());
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }

            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }
}
