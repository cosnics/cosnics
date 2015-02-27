<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestionOption;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\ComplexAssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_multiple_choice_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class AssessmentMultipleChoiceQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $complex_content_object_question = $this->get_complex_content_object_question();
        $feedback_options_type = $complex_content_object_question->get_feedback_options();
        $question = $this->get_question();
        
        $html = array();
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox_answer"></th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        
        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && $question->has_feedback() &&
             ! $this->can_change())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $answers = $this->get_answers();
        $options = $question->get_options();
        $type = $question->get_answer_type();
        
        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            if ($type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
            {
                if (in_array($i, $answers))
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
                
                $html[] = '<td><input type="radio" name="yourchoice_' .
                     $this->get_complex_content_object_question()->get_id() . '" value="' . $i . '" disabled' . $selected .
                     '/>' . $result . '</td>';
            }
            else
            {
                $was_checked = array_key_exists($i + 1, $answers);
                $is_correct = $option->is_correct();
                
                if ($was_checked)
                {
                    $selected = ' checked ';
                }
                else
                {
                    $selected = '';
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
                
                $html[] = '<td><input type="checkbox" name="yourchoice' . $i . '" disabled' . $selected . '/>' . $result .
                     '</td>';
            }
            
            $object_renderer = new ContentObjectResourceRenderer($this->get_results_viewer(), $option->get_value());
            $html[] = '<td>' . $object_renderer->run() . '</td>';
            
            if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() &&
                 ($option->has_feedback() || $question->has_feedback()) && ! $this->can_change())
            {
                if ($feedback_options_type == ComplexAssessmentMultipleChoiceQuestion :: FEEDBACK_OPTIONS_ALL)
                {
                    $feedback_options = true;
                }
                elseif ($feedback_options_type == ComplexAssessmentMultipleChoiceQuestion :: FEEDBACK_OPTIONS_SELECTED &&
                     in_array($i, $answers) && $type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
                {
                    $feedback_options = true;
                }
                elseif ($feedback_options_type == ComplexAssessmentMultipleChoiceQuestion :: FEEDBACK_OPTIONS_SELECTED &&
                     array_key_exists($i + 1, $answers) &&
                     $type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
                {
                    $feedback_options = true;
                }
                else
                {
                    $feedback_options = false;
                }
                
                if (AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
                {
                    $correct_options = array();
                    
                    foreach ($options as $key => $correct_option)
                    {
                        if ($correct_option->is_correct())
                        {
                            $correct_options[] = $key + 1;
                        }
                    }
                }
                
                $no_difference = (count(array_diff($answers, $correct_options)) == 0) &&
                     (count(array_diff($correct_options, $answers)) == 0);
                
                $valid_answer = ($type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO &&
                     $options[$answers[0]] instanceof AssessmentMultipleChoiceQuestionOption &&
                     $options[$answers[0]]->is_correct()) || ($type ==
                     AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX && $no_difference);
                
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
