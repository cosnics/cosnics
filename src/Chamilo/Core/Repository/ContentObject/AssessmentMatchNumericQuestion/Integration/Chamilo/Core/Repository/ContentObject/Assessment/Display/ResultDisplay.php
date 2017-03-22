<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package
 *          core\repository\content_object\assessment_match_numeric_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
{

    public function get_question_result()
    {
        $best_option = $this->get_question()->get_best_option();
        $best_answer = $this->get_score() == $best_option->get_score();
        $valid_answer = $this->get_score() > 0;
        $user_answer = $this->get_answers();
        $answer_option =
            $this->get_question()->get_option($user_answer[0], $this->get_question()->get_tolerance_type());
        $configuration = $this->getViewerApplication()->get_configuration();

        $html = array();

        $html[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th style="width: 50%;">' .
            Translation::get('YourAnswer', null, 'Chamilo\Core\Repository\ContentObject\Assessment') . '</th>';

        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation::get('Feedback', null, 'Chamilo\Core\Repository\ContentObject\Assessment') .
                '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $html[] = '<tr class="row_even">';

        if (!is_null($user_answer[0]) && $user_answer[0] != '')
        {
            if ($configuration->show_correction() || $configuration->show_solution())
            {
                if ($valid_answer &&
                    ($best_option->matches($user_answer[0], $this->get_question()->get_tolerance_type()) ||
                        $best_option->get_score() == $this->get_score())
                )
                {
                    $result = ' <img style="vertical-align: middle;" src="' .
                        Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') . '" alt="' .
                        Translation::get('Correct') . '" title="' .
                        Translation::get('Correct', null, 'Chamilo\Core\Repository\ContentObject\Assessment') .
                        '" style="" />';
                }
                elseif ($valid_answer)
                {
                    $result = ' <img style="vertical-align: middle;" src="' .
                        Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWarning') . '" alt="' .
                        Translation::get('CorrectButNotBest') . '" title="' . Translation::get(
                            'CorrectButNotBest', null, 'Chamilo\Core\Repository\ContentObject\Assessment'
                        ) .
                        '" style="" />';
                }
                else
                {
                    $result = ' <img style="vertical-align: middle;" src="' .
                        Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') . '" alt="' .
                        Translation::get('Wrong') . '" title="' .
                        Translation::get('Wrong', null, 'Chamilo\Core\Repository\ContentObject\Assessment') . '" />';
                }
            }
            else
            {
                $result = '';
            }

            $html[] = '<td>' . $user_answer[0] . $result . '</td>';
        }
        else
        {
            if ($configuration->show_correction() || $configuration->show_solution())
            {
                $result = ' <img style="vertical-align: middle;" src="' .
                    Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') . '" alt="' .
                    Translation::get('Wrong') . '" title="' .
                    Translation::get('Wrong', null, 'Chamilo\Core\Repository\ContentObject\Assessment') . '" />';
            }
            else
            {

                $result = '';
            }
            $html[] = '<td>' . Translation::get('NoAnswer', null, 'Chamilo\Core\Repository\ContentObject\Assessment') .
                $result . '</td>';
        }

        if (AnswerFeedbackDisplay::allowed(
            $configuration,
            $this->get_complex_content_object_question(),
            true,
            $valid_answer
        )
        )
        {
            if (!is_null($answer_option))
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->getViewerApplication(),
                    $answer_option->get_feedback()
                );
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }
            else
            {
                $html[] = '<td>-</td>';
            }
        }

        $html[] = '</tr>';

        $html[] = '</tbody>';
        $html[] = '</table>';

        if ($configuration->show_solution())
        {
            if (!$valid_answer || ($valid_answer && !$best_option->matches(
                        $user_answer[0],
                        $this->get_question()->get_tolerance_type()
                    )
                    && $best_option->get_score() != $this->get_score()
                )
            )
            {
                $html[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
                $html[] = '<thead>';
                $html[] = '<tr>';
                $html[] = '<th style="width: 50%;">' .
                    Translation::get('BestPossibleAnswer', null, 'Chamilo\Core\Repository\ContentObject\Assessment') .
                    '</th>';

                $answer_feedback_display = AnswerFeedbackDisplay::allowed(
                    $configuration,
                    $this->get_complex_content_object_question(),
                    false,
                    true
                );

                if ($answer_feedback_display)
                {
                    $html[] = '<th>' .
                        Translation::get('Feedback', null, 'Chamilo\Core\Repository\ContentObject\Assessment') .
                        '</th>';
                }

                $html[] = '</tr>';
                $html[] = '</thead>';
                $html[] = '<tbody>';

                $html[] = '<tr class="row_even">';
                $html[] = '<td>' . $best_option->get_value() . '</td>';

                if ($answer_feedback_display)
                {
                    $object_renderer = new ContentObjectResourceRenderer(
                        $this->getViewerApplication(),
                        $best_option->get_feedback()
                    );
                    $html[] = '<td>' . $object_renderer->run() . '</td>';
                }

                $html[] = '</tr>';
                $html[] = '</tbody>';
                $html[] = '</table>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
