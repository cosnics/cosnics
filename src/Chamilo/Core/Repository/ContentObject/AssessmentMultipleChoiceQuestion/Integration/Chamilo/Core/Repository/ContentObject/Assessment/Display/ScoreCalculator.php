<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;

/**
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
class ScoreCalculator extends \Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\ScoreCalculator
{

    public function calculate_score()
    {
        $user_answers = $this->get_answer();

        if(!is_array($user_answers))
        {
            $user_answers = [];
        }

        $question = $this->get_question();
        if ($question->get_answer_type() == AssessmentMultipleChoiceQuestion::ANSWER_TYPE_RADIO)
        {
            $answers = $question->get_options();
            $selected = $answers[$user_answers[0]];
            if ($selected && $selected->is_correct())
            {
                $score = $selected->get_score();
                $total_weight = $selected->get_score();
            }
            else
            {
                // Treat score of correct answer as weight
                foreach ($answers as $answer)
                {
                    if ($answer->is_correct())
                    {
                        $total_weight = $answer->get_score();
                        break;
                    }
                }
                if ($selected)
                {
                    $score = $selected->get_score();
                }
                else
                {
                    $score = 0;
                }
            }
        }
        else
        {
            $answers = $question->get_options();
            $score = 0;
            $total_weight = 0;

            foreach ($answers as $i => $answer)
            {
                if (array_key_exists($i + 1, $user_answers))
                {
                    $score += $answer->get_score();
                }

                if ($answer->is_correct())
                {
                    $total_weight += $answer->get_score();
                }
            }
        }
        return $this->make_score_relative($score, $total_weight);
    }
}
