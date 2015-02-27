<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\ComplexAssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_select_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class AssessmentSelectQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $complex_content_object_question = $this->get_complex_content_object_question();
        $feedback_options_type = $complex_content_object_question->get_feedback_options();
        
        $html = array();
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox_answer"></th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        
        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        $type = $this->get_question()->get_answer_type();
        $answers = $this->get_answers();
        
        if ($type == AssessmentSelectQuestion :: ANSWER_TYPE_RADIO)
        {
            foreach ($this->get_question()->get_options() as $i => $option)
            {
                $options[$i] = $option;
            }
        }
        else
        {
            $options = $this->get_question()->get_options();
        }
        
        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            if ($type == AssessmentSelectQuestion :: ANSWER_TYPE_RADIO)
            {
                if ($answers[0] == $i)
                {
                    $selected = ' checked ';
                    
                    if ($option->is_correct())
                    {
                        $result = '<img src="' . Theme :: getInstance()->getImagePath() . 'answer_correct.png" alt="' . Translation :: get(
                            'Correct') . '" title="' . Translation :: get('Correct') . '" style="" />';
                    }
                    else
                    {
                        $result = '<img src="' . Theme :: getInstance()->getImagePath() . 'answer_wrong.png" alt="' . Translation :: get(
                            'Wrong') . '" title="' . Translation :: get('Wrong') . '" />';
                    }
                }
                else
                {
                    $selected = '';
                    
                    if ($option->is_correct())
                    {
                        $result = '<img src="' . Theme :: getInstance()->getImagePath() . 'answer_correct.png" alt="' . Translation :: get(
                            'Correct') . '" title="' . Translation :: get('Correct') . '" />';
                    }
                    else
                    {
                        $result = '';
                    }
                }
                
                $html[] = '<td>' . '<input type="radio" name="yourchoice_' .
                     $this->get_complex_content_object_question()->get_id() . '" value="' . $i . '" disabled' . $selected .
                     '/>' . $result . '</td>';
            }
            else
            {
                $was_checked = in_array($i, $answers[0]);
                $is_correct = $option->is_correct();
                
                if ($was_checked)
                {
                    $selected = " checked ";
                }
                else
                {
                    $selected = "";
                }
                
                if ($is_correct)
                {
                    $result = '<img src="' . Theme :: getInstance()->getImagePath() . 'answer_correct.png" alt="' . Translation :: get(
                        'Correct') . '" title="' . Translation :: get('Correct') . '" style="" />';
                }
                else
                {
                    $result = '<img src="' . Theme :: getInstance()->getImagePath() . 'answer_wrong.png" alt="' . Translation :: get(
                        'Wrong') . '" title="' . Translation :: get('Wrong') . '" />';
                }
                
                $html[] = '<td>' . '<input type="checkbox" name="yourchoice' . $i . '" disabled' . $selected . '/>' .
                     $result . '</td>';
            }
            
            $html[] = '<td>' . $option->get_value() . '</td>';
            
            if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
            {
                $feedback_options = $feedback_options_type == ComplexAssessmentSelectQuestion :: FEEDBACK_OPTIONS_ALL || ($feedback_options_type ==
                     ComplexAssessmentSelectQuestion :: FEEDBACK_OPTIONS_SELECTED && $answers[0] == $i &&
                     $type == AssessmentSelectQuestion :: ANSWER_TYPE_RADIO) || ($feedback_options_type ==
                     ComplexAssessmentSelectQuestion :: FEEDBACK_OPTIONS_SELECTED && in_array($i, $answers[0]) &&
                     $type == AssessmentSelectQuestion :: ANSWER_TYPE_CHECKBOX);
                
                if (AssessmentSelectQuestion :: ANSWER_TYPE_CHECKBOX)
                {
                    $correct_options = array();
                    
                    foreach ($options as $key => $correct_option)
                    {
                        if ($correct_option->is_correct())
                        {
                            $correct_options[] = $key;
                        }
                    }
                }
                
                $no_difference = (count(array_diff($answers[0], $correct_options)) == 0) &&
                     (count(array_diff($correct_options, $answers[0])) == 0);
                
                $valid_answer = ($type == AssessmentSelectQuestion :: ANSWER_TYPE_RADIO &&
                     ! is_null($options[$answers[0]]) && $options[$answers[0]]->is_correct()) ||
                     ($type == AssessmentSelectQuestion :: ANSWER_TYPE_CHECKBOX && $no_difference);
                
                $feedback_answer = ($this->get_complex_content_object_question()->get_feedback_answer() &&
                     ! $valid_answer) || ! $this->get_complex_content_object_question()->get_feedback_answer();
                
                if ($feedback_options && $feedback_answer)
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
