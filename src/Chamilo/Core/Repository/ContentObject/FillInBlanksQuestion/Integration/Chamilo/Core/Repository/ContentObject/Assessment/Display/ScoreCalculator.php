<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

/**
 * $Id: fill_in_blanks_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
class ScoreCalculator extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\Inc\AssessmentQuestionResultDisplay
{

    public function calculate_score()
    {
        $user_answers = $this->get_answer();
        
        $total_score = 0;
        
        foreach ($user_answers as $position => $answer_value)
        {
            $total_score += $this->get_question()->get_weight_from_answer($position, $answer_value);
        }
        
        $total_weight = $this->get_question()->get_maximum_score();
        return $this->make_score_relative($total_score, $total_weight);
    }
}
