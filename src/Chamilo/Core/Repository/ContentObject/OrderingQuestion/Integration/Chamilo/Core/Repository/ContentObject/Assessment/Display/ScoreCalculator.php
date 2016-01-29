<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

/**
 * $Id: ordering_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
class ScoreCalculator extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\ScoreCalculator
{

    public function calculate_score()
    {
        $user_answers = $this->get_answer();
        
        $answers = $this->get_question()->get_options();
        
        $score = 0;
        $total_weight = 0;
        
        foreach ($answers as $i => $answer)
        {
            if ($user_answers[$i + 1] == $answer->get_order())
            {
                $score += $answer->get_score();
            }
            
            $total_weight += $answer->get_score();
        }
        
        return $this->make_score_relative($score, $total_weight);
    }
}
