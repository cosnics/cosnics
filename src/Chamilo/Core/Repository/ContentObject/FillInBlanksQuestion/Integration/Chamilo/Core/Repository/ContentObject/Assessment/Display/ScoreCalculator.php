<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

/**
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
class ScoreCalculator extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\ScoreCalculator
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
