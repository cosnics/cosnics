<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

/**
 * $Id: assessment_rating_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
class ScoreCalculator extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\ScoreCalculator
{

    public function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();
        
        if ($question->get_correct() == $user_answers[0])
        {
            $score = 1;
        }
        else
        {
            $score = 0;
        }
        
        return $this->make_score_relative($score, 1);
    }
}
