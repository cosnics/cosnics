<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestionOption;

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
            $user_answers = [];

        $user_answers = $user_answers[0];

        $question = $this->get_question();
        if ($question->get_answer_type() == 'radio')
        {
            if ($user_answers == - 1)
            {
                return 0;
            }

            $answers = $question->get_options();
            $selected = $answers[$user_answers];

            if ($selected instanceof AssessmentSelectQuestionOption && $selected->is_correct())
            {
                return $this->make_score_relative($selected->get_score(), $selected->get_score());
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
                if ($selected instanceof AssessmentSelectQuestionOption && $selected->get_score() < 0)
                {
                    $score = $selected->get_score();
                }
                else
                {
                    $score = 0;
                }

                return $this->make_score_relative($score, $total_weight);
            }
        }
        else
        {
            $answers = $question->get_options();

            $score = 0;
            $total_weight = 0;

            foreach ($answers as $i => $answer)
            {
                if (in_array($i, $user_answers) && ($answer->is_correct() || $answer->get_score() <= 0))
                {
                    $score += $answer->get_score();
                }

                if ($answer->is_correct())
                {
                    $total_weight += $answer->get_score();
                }
            }
            return $this->make_score_relative($score, $total_weight);
        }
    }
}
