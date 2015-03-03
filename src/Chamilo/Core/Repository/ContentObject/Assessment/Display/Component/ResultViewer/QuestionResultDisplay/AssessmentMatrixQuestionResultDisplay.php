<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_matrix_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class AssessmentMatrixQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $answers = $this->get_answers();
        $options = $this->get_question()->get_options();
        $matches = $this->get_question()->get_matches();
        $type = $this->get_question()->get_matrix_type();

        $html = array();
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';

        foreach ($matches as $match)
        {
            $html[] = '<th style="text-transform: none; font-size: small;">' . $match . '</th>';
        }

        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';

            $object_renderer = new ContentObjectResourceRenderer($this->get_results_viewer(), $option->get_value());
            $html[] = '<td>' . $object_renderer->run() . '</td>';

            foreach ($matches as $j => $match)
            {
                $html[] = '<td>';
                if ($type == AssessmentMatrixQuestion :: MATRIX_TYPE_RADIO)
                {
                    if ($answers[$i] == $j && ! is_null($answers[$i]))
                    {
                        $selected = " checked ";

                        if ($option->get_matches() == $j)
                        {
                            $result = '<img src="' . Theme :: getInstance()->getImagePath(
                                'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                                'AnswerCorrect') . '" alt="' . Translation :: get('Correct') . '" title="' .
                                 Translation :: get('Correct') . '" />';
                        }
                        else
                        {
                            $result = '<img src="' . Theme :: getInstance()->getImagePath(
                                'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                                'AnswerWrong') . '" alt="' . Translation :: get('Wrong') . '" title="' .
                                 Translation :: get('Wrong') . '" />';
                        }
                    }
                    else
                    {
                        $selected = '';
                        if ($option->get_matches() == $j)
                        {
                            $result = '<img src="' . Theme :: getInstance()->getImagePath(
                                'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                                'AnswerCorrect') . '" alt="' . Translation :: get('Correct') . '" title="' .
                                 Translation :: get('Correct') . '" />';
                        }
                        else
                        {
                            $result = '';
                        }
                    }

                    $html[] = '<input type="radio" name="yourchoice_' .
                         $this->get_complex_content_object_question()->get_id() . '_' . $i . '" value="' . $j .
                         '" disabled' . $selected . '/>';
                    $html[] = $result;
                }
                else
                {
                    if (array_key_exists($j, $answers[$i]))
                    {
                        $selected = " checked ";

                        if (in_array($j, $option->get_matches()))
                        {
                            $result = Theme :: getInstance()->getCommonImage('action_confirm');
                        }
                        else
                        {
                            $result = Theme :: getInstance()->getCommonImage('action_delete');
                        }
                    }
                    else
                    {
                        $selected = '';

                        if (in_array($j, $option->get_matches()))
                        {
                            $result = Theme :: getInstance()->getCommonImage('action_metadata');
                        }
                        else
                        {
                            $result = '';
                        }
                    }

                    $html[] = '<input type="checkbox" name="yourchoice_' . $i . '_' . $j . '" disabled' . $selected .
                         '/>';
                    $html[] = $result;
                }

                $html[] = '</td>';
            }

            if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
            {
                $valid_answer = ($type == AssessmentMatrixQuestion :: MATRIX_TYPE_RADIO &&
                     $answers[$i] == $option->get_matches()) || ($type ==
                     AssessmentMatrixQuestion :: MATRIX_TYPE_CHECKBOX &&
                     count(array_diff(array_keys($answers[$i]), $option->get_matches())) == 0);

                if (($this->get_complex_content_object_question()->get_feedback_answer() && ! $valid_answer) ||
                     ! $this->get_complex_content_object_question()->get_feedback_answer())
                {
                    $object_renderer = new ContentObjectResourceRenderer(
                        $this->get_results_viewer(),
                        $option->get_feedback());
                    $html[] = '<td>' . $object_renderer->run() . '</td>';
                }
                else
                {
                    $html[] = '<td></td>';
                }
            }

            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }
}
