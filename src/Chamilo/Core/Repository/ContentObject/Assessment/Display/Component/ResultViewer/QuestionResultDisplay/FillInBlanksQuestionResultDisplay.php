<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestionAnswer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;

/**
 * $Id: fill_in_blanks_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class FillInBlanksQuestionResultDisplay extends QuestionResultDisplay
{

    /**
     *
     * @var string
     */
    private $parts;

    /**
     *
     * @var array
     */
    private $feedback_answer = array();

    public function display_question_result()
    {
        $answers = $this->get_answers();
        
        $answer_text = $this->get_question()->get_answer_text();
        $answer_text = nl2br($answer_text);
        $this->parts = preg_split(FillInBlanksQuestionAnswer :: QUESTIONS_REGEX, $answer_text);
        
        $html[] = '<div class="with_borders">';
        $html[] = array_shift($this->parts);
        
        $text = array();
        $index = 0;
        foreach ($this->parts as $i => $part)
        {
            switch ($this->get_question()->is_correct($i, $answers[$i]))
            {
                case FillInBlanksQuestion :: MARK_MAX :
                    $text[] = '<span style="color:green"><b>' . $answers[$i] . '</b></span>';
                    break;
                case FillInBlanksQuestion :: MARK_CORRECT :
                    $text[] = '<span style="color:orange"><b>' . $answers[$i] . '</b></span>';
                    break;
                case FillInBlanksQuestion :: MARK_WRONG :
                    $best_answer = $this->get_question()->get_best_answer_for_question($index);
                    $best_answer_text = $best_answer->get_value();
                    $text[] = '<span style="color:green"><b>' . $best_answer_text . '</b></span>';
                    break;
            }
            
            $text[] = $part;
            $index ++;
        }
        
        $html[] = implode('', $text);
        $html[] = '</div>';
        
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        
        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }
        
        if ($this->get_results_viewer()->get_configuration()->show_score() && ! $this->can_change())
        {
            $html[] = '<th class="empty">' . Translation :: get('Score') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        foreach ($this->parts as $index => $part)
        {
            $html[] = $this->get_question_feedback($index, $answers[$index], count($this->parts) > 1);
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }

    public function get_question_feedback($index, $answer, $multiple_answers)
    {
        $row_count = 0;
        
        $question = $this->get_question();
        $correct = $question->is_correct($index, $answer);
        $best_answer = $question->get_best_answer_for_question($index);
        $complex_content_object_question = $this->get_complex_content_object_question();
        
        $feedback_options_type = $complex_content_object_question->get_show_answer_feedback();
        $all_feedback_options = $feedback_options_type == Configuration :: ANSWER_FEEDBACK_TYPE_ALL;
        
        $is_first_option = true;
        
        switch ($correct)
        {
            case FillInBlanksQuestion :: MARK_MAX :
                // Selected answer = best answer
                $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                $html[] = '<td>' . ($index + 1) . '.</td>';
                $show_answer = empty($answer) ? Translation :: get('NoAnswer') : $answer;
                $html[] = '<td>' . Translation :: get('UserAnswer') . ': <span style="color:green"><b>' . $show_answer .
                     '</b></span></td>';
                
                if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
                {
                    // get the comment
                    $answer_object = $question->get_answer_object($index, $answer);
                    $comment = $answer_object && $answer_object->has_comment() ? $answer_object->get_comment() : '-';
                    
                    $html[] = '<td>';
                    $html[] = ! $this->get_complex_content_object_question()->get_feedback_answer() ? $comment : '-';
                    $html[] = '</td>';
                }
                
                if ($this->get_results_viewer()->get_configuration()->show_score() && ! $this->can_change())
                {
                    $weight = $question->get_weight_from_answer($index, $answer);
                    $max_question_weight = $question->get_question_maximum_weight($index);
                    $html[] = '<td>' . $weight . ' / ' . $max_question_weight . '</td>';
                }
                
                $html[] = '</tr>';
                
                break;
            case FillInBlanksQuestion :: MARK_CORRECT :
                // Selected answer
                $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . '">' . ($index + 1) . '.</td>';
                $show_answer = empty($answer) ? Translation :: get('NoAnswer') : $answer;
                $html[] = '<td>' . Translation :: get('UserAnswer') . ': <span style="color:orange"><b>' . $show_answer .
                     '</b></span></td>';
                
                if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
                {
                    // get the comment
                    $answer_object = $question->get_answer_object($index, $answer);
                    $comment = $answer_object && $answer_object->has_comment() ? $answer_object->get_comment() : '-';
                    
                    $html[] = '<td>';
                    $html[] = $comment;
                    $html[] = '</td>';
                }
                
                if ($this->get_results_viewer()->get_configuration()->show_score() && ! $this->can_change())
                {
                    $weight = $question->get_weight_from_answer($index, $answer);
                    $max_question_weight = $question->get_question_maximum_weight($index);
                    $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . '">' . $weight . ' / ' .
                         $max_question_weight . '</td>';
                }
                
                $html[] = '</tr>';
                
                if ($all_feedback_options)
                {
                    // Best answer
                    $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                    
                    $show_answer = $best_answer->get_value();
                    $show_answer = empty($show_answer) ? Translation :: get('NoAnswer') : $best_answer->get_value();
                    $number_of_positive_answers = $question->count_positive_answers($index);
                    $show_answer = Translation :: get(
                        $number_of_positive_answers > 1 ? 'BestAnswerWas' : 'AnswerWas', 
                        array('ANSWER' => $show_answer));
                    
                    $html[] = '<td>' . $show_answer . '</td>';
                    
                    if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
                    {
                        $comment = $best_answer->has_comment() ? $best_answer->get_comment() : '-';
                        
                        $html[] = '<td>';
                        $html[] = ! $this->get_complex_content_object_question()->get_feedback_answer() ? $comment : '-';
                        $html[] = '</td>';
                    }
                    
                    $html[] = '</tr>';
                }
                break;
            case FillInBlanksQuestion :: MARK_WRONG :
                // Selected answer
                $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . '">' . ($index + 1) . '.</td>';
                $show_answer = empty($answer) ? Translation :: get('NoAnswer') : $answer;
                
                $percentage = 0;
                if (! $question->get_best_answer_for_question($index)->check_regex() &&
                     $question->get_question_type() != FillInBlanksQuestion :: TYPE_SELECT)
                {
                    // always do similarity checks caseinsensitive.
                    similar_text(
                        mb_strtolower($answer, 'UTF-8'), 
                        mb_strtolower($question->get_best_answer_for_question($index)->get_value(), 'UTF-8'), 
                        $percentage);
                }
                
                $colour = $percentage >= 70 ? 'orange' : 'red';
                
                $html[] = '<td>' . Translation :: get('UserAnswer') . ': <span style="color:' . $colour . '"><b>' .
                     $show_answer . '</b></span></td>';
                
                if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
                {
                    // get the comment
                    $answer_object = $question->get_answer_object($index, $answer);
                    $comment = $answer_object && $answer_object->has_comment() ? $answer_object->get_comment() : '-';
                    
                    $html[] = '<td>';
                    $html[] = $comment;
                    $html[] = '</td>';
                }
                
                if ($this->get_results_viewer()->get_configuration()->show_score() && ! $this->can_change())
                {
                    $weight = $question->get_weight_from_answer($index, $answer);
                    $max_question_weight = $question->get_question_maximum_weight($index);
                    $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . 'a">' . $weight . ' / ' .
                         $max_question_weight . '</td>';
                }
                
                $html[] = '</tr>';
                
                if ($all_feedback_options)
                {
                    // Best answer
                    $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                    
                    $show_answer = $best_answer->get_value();
                    $show_answer = empty($show_answer) ? Translation :: get('NoAnswer') : $best_answer->get_value();
                    $number_of_positive_answers = $question->count_positive_answers($index);
                    $show_answer = Translation :: get(
                        $number_of_positive_answers > 1 ? 'BestAnswerWas' : 'AnswerWas', 
                        array('ANSWER' => $show_answer));
                    
                    $html[] = '<td>' . $show_answer . '</td>';
                    
                    if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
                    {
                        $comment = $best_answer->has_comment() ? $best_answer->get_comment() : '-';
                        
                        $html[] = '<td>';
                        $html[] = ! $this->get_complex_content_object_question()->get_feedback_answer() ? $comment : '-';
                        $html[] = '</td>';
                    }
                    
                    $html[] = '</tr>';
                }
                break;
        }
        
        return implode(PHP_EOL, $html);
    }
}
