<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

/**
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
class ScoreCalculator extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\ScoreCalculator
{

    public function calculate_score()
    {
        $user_answers = $this->get_answer();
        $user_answer = trim($user_answers[0]);
        $question = $this->get_question();
        $use_wildcards = $question->get_use_wildcards();
        $ignore_case = $question->get_ignore_case();
        $max_score = $question->get_best_option()->get_score();
        $options = $question->get_options();

        usort($options, function($optionA, $optionB) {
            $scoreA = $optionA->get_score();
            $scoreB = $optionB->get_score();

            if ($scoreA == $scoreB) {
                return 0;
            }
            return ($scoreA < $scoreB) ? 1 : -1;
        });

        $result = 0;
        foreach ($options as $option)
        {
            if ($option->matches($user_answer, $ignore_case, $use_wildcards))
            {
                return $this->make_score_relative($option->get_score(), $max_score);
            }
        }
        return $result;
    }
}
