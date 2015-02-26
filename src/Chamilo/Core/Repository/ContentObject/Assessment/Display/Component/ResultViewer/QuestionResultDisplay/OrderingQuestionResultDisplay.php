<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: ordering_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class OrderingQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th style="text-align: center;" class="list">' . Translation :: get('UserOrder') . '</th>';
        $html[] = '<th style="text-align: center;" class="list">' . Translation :: get('CorrectOrder') . '</th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        
        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $answers = $this->get_question()->get_options();
        $user_answers = $this->get_answers();
        
        foreach ($answers as $i => $answer)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            $correct_answer = $user_answers[$i + 1] == $answer->get_order();
            
            if ($correct_answer)
            {
                $result = ' <img style="vertical-align: middle;" src="' . Theme :: getInstance()->getImagePath() .
                     'answer_correct.png" alt="' . Translation :: get('Correct') . '" title="' .
                     Translation :: get('Correct') . '" style="" />';
            }
            else
            {
                $result = ' <img style="vertical-align: middle;" src="' . Theme :: getInstance()->getImagePath() .
                     'answer_wrong.png" alt="' . Translation :: get('Wrong') . '" title="' . Translation :: get('Wrong') .
                     '" />';
            }
            
            if ($user_answers[$i + 1] == - 1)
            {
                $html[] = '<td style="text-align: center;">' . Translation :: get('NoAnswer') . $result . '</td>';
            }
            else
            {
                $html[] = '<td style="text-align: center;">' . $user_answers[$i + 1] . $result . '</td>';
            }
            $html[] = '<td style="text-align: center;">' . $answer->get_order() . '</td>';
            
            $object_renderer = new ContentObjectResourceRenderer($this->get_results_viewer(), $answer->get_value());
            
            $html[] = '<td>' . $object_renderer->run() . '</td>';
            
            if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->get_results_viewer(), 
                    $answer->get_feedback());
                
                if (! $correct_answer)
                {
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
        
        return implode("\n", $html);
    }
}
