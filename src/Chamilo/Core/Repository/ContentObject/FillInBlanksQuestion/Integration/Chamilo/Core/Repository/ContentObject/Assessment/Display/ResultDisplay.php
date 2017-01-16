<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestionAnswer;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package
 *          core\repository\content_object\fill_in_blanks_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
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

    public function get_question_result()
    {
        $answers = $this->get_answers();
        $configuration = $this->getViewerApplication()->get_configuration();

        $answer_text = $this->get_question()->get_answer_text();
        $answer_text = nl2br($answer_text);
        $this->parts = preg_split(FillInBlanksQuestionAnswer::QUESTIONS_REGEX, $answer_text);

        $html[] = '<div class="fill-in-the-blanks-result">';
        $html[] = array_shift($this->parts);

        $text = array();
        $index = 0;
        foreach ($this->parts as $i => $part)
        {
            if ($configuration->show_correction() || $configuration->show_solution())
            {
                switch ($this->get_question()->is_correct($i, $answers[$i]))
                {
                    case FillInBlanksQuestion::MARK_MAX :
                        $text[] = '<span style="color:green"><b>' . $answers[$i] . '</b></span>';
                        break;
                    case FillInBlanksQuestion::MARK_CORRECT :
                        $text[] = '<span style="color:orange"><b>' . $answers[$i] . '</b></span>';
                        break;
                    case FillInBlanksQuestion::MARK_WRONG :
                        $best_answer = $this->get_question()->get_best_answer_for_question($index);
                        $best_answer_text = $best_answer->get_value();
                        $text[] = '<span style="color:green"><b>' . $best_answer_text . '</b></span>';
                        break;
                }
            }
            else
            {
                $text[] = '<b>' . $answers[$i] . '</b>';
            }

            $text[] = $part;
            $index ++;
        }

        $html[] = implode('', $text);
        $html[] = '</div>';

        $html[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';

        $html[] = '<th>' . $this->getAssessmentTranslation('Answer') . '</th>';

        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . $this->getAssessmentTranslation('Feedback') . '</th>';
        }

        if ($configuration->show_score())
        {
            $html[] = '<th class="empty">' . $this->getAssessmentTranslation('Score') . '</th>';
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
        $feedback_options_type = $this->getViewerApplication()->get_configuration();
        $all_feedback_options = $feedback_options_type == Configuration::ANSWER_FEEDBACK_TYPE_ALL;
        $configuration = $this->getViewerApplication()->get_configuration();

        $is_first_option = true;

        switch ($correct)
        {
            case FillInBlanksQuestion::MARK_MAX :
                // Selected answer = best answer
                $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                $html[] = '<td>' . ($index + 1) . '.</td>';
                $show_answer = empty($answer) ? $this->getAssessmentTranslation('NoAnswer') : $answer;

                if ($configuration->show_correction() || $configuration->show_solution())
                {
                    $html[] =
                        '<td>' . $this->getAssessmentTranslation('YourAnswer') . ': <span style="color:green"><b>' . $show_answer .
                        '</b></span></td>';
                }
                else
                {
                    $html[] = '<td>' . $this->getAssessmentTranslation('YourAnswer') . ': <b>' . $show_answer . '</b></td>';
                }

                if (AnswerFeedbackDisplay::allowed(
                    $configuration,
                    $this->get_complex_content_object_question(),
                    true,
                    true
                )
                )
                {
                    // get the comment
                    $answer_object = $question->get_answer_object($index, $answer);
                    $comment = $answer_object && $answer_object->has_comment() ? $answer_object->get_comment() : '-';

                    $html[] = '<td>' . $comment . '</td>';
                }

                if ($configuration->show_score())
                {
                    $weight = $question->get_weight_from_answer($index, $answer);
                    $max_question_weight = $question->get_question_maximum_weight($index);
                    $html[] = '<td>' . $weight . ' / ' . $max_question_weight . '</td>';
                }

                $html[] = '</tr>';

                break;
            case FillInBlanksQuestion::MARK_CORRECT :
                // Selected answer
                $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . '">' . ($index + 1) . '.</td>';
                $show_answer = empty($answer) ? $this->getAssessmentTranslation('NoAnswer') : $answer;

                if ($configuration->show_correction() || $configuration->show_solution())
                {
                    $html[] =
                        '<td>' . $this->getAssessmentTranslation('YourAnswer') . ': <span style="color:orange"><b>' . $show_answer .
                        '</b></span></td>';
                }
                else
                {
                    $html[] = '<td>' . $this->getAssessmentTranslation('YourAnswer') . ': <b>' . $show_answer . '</b></td>';
                }

                if (AnswerFeedbackDisplay::allowed(
                    $configuration,
                    $this->get_complex_content_object_question(),
                    true,
                    false
                )
                )
                {
                    // get the comment
                    $answer_object = $question->get_answer_object($index, $answer);
                    $comment = $answer_object && $answer_object->has_comment() ? $answer_object->get_comment() : '-';

                    $html[] = '<td>' . $comment . '</td>';
                }

                if ($configuration->show_score())
                {
                    $weight = $question->get_weight_from_answer($index, $answer);
                    $max_question_weight = $question->get_question_maximum_weight($index);
                    $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . '">' . $weight . ' / ' .
                        $max_question_weight . '</td>';
                }

                $html[] = '</tr>';

                if ($configuration->show_solution())
                {
                    // Best answer
                    $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                    $html[] = '<td></td>';

                    $show_answer = $best_answer->get_value();
                    $show_answer = empty($show_answer) ? $this->getAssessmentTranslation('NoAnswer') : $best_answer->get_value();
                    $number_of_positive_answers = $question->count_positive_answers($index);
                    $show_answer = $this->getAssessmentTranslation(
                        $number_of_positive_answers > 1 ? 'BestAnswerWas' : 'AnswerWas',
                        array('ANSWER' => $show_answer)
                    );

                    $html[] = '<td>' . $show_answer . '</td>';

                    if (AnswerFeedbackDisplay::allowed(
                        $configuration,
                        $this->get_complex_content_object_question(),
                        true,
                        true
                    )
                    )
                    {
                        $comment = $best_answer->has_comment() ? $best_answer->get_comment() : '-';

                        $html[] = '<td>' . $comment . '</td>';
                    }

                    if ($configuration->show_score())
                    {
                        $html[] = '<td></td>';
                    }

                    $html[] = '</tr>';
                }
                break;
            case FillInBlanksQuestion::MARK_WRONG :
                // Selected answer
                $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . '">' . ($index + 1) . '.</td>';
                $show_answer = empty($answer) ? $this->getAssessmentTranslation('NoAnswer') : $answer;

                if ($configuration->show_correction() || $configuration->show_solution())
                {
                    $percentage = 0;
                    if (!$question->get_best_answer_for_question($index)->check_regex() &&
                        $question->get_question_type() != FillInBlanksQuestion::TYPE_SELECT
                    )
                    {
                        // always do similarity checks caseinsensitive.
                        similar_text(
                            mb_strtolower($answer, 'UTF-8'),
                            mb_strtolower($question->get_best_answer_for_question($index)->get_value(), 'UTF-8'),
                            $percentage
                        );
                    }

                    $colour = $percentage >= 70 ? 'orange' : 'red';
                    $html[] = '<td>' . $this->getAssessmentTranslation('YourAnswer') . ': <span style="color:' . $colour . '"><b>' .
                        $show_answer . '</b></span></td>';
                }
                else
                {
                    $html[] = '<td>' . $this->getAssessmentTranslation('YourAnswer') . ': <b>' . $show_answer . '</b></td>';
                }

                if (AnswerFeedbackDisplay::allowed(
                    $configuration,
                    $this->get_complex_content_object_question(),
                    true,
                    false
                )
                )
                {
                    // get the comment
                    $answer_object = $question->get_answer_object($index, $answer);
                    $comment = $answer_object && $answer_object->has_comment() ? $answer_object->get_comment() : '-';

                    $html[] = '<td>' . $comment . '</td>';
                }

                if ($configuration->show_score())
                {
                    $weight = $question->get_weight_from_answer($index, $answer);
                    $max_question_weight = $question->get_question_maximum_weight($index);
                    $html[] = '<td rowspan="' . ($all_feedback_options ? '2' : '1') . 'a">' . $weight . ' / ' .
                        $max_question_weight . '</td>';
                }

                $html[] = '</tr>';

                if ($configuration->show_solution())
                {
                    // Best answer
                    $html[] = '<tr class="' . ($row_count % 2 == 0 ? 'row_even' : 'row_odd') . '">';
                    $html[] = '<td></td>';

                    $show_answer = $best_answer->get_value();
                    $show_answer = empty($show_answer) ? $this->getAssessmentTranslation('NoAnswer') : $best_answer->get_value();
                    $number_of_positive_answers = $question->count_positive_answers($index);
                    $show_answer = $this->getAssessmentTranslation(
                        $number_of_positive_answers > 1 ? 'BestAnswerWas' : 'AnswerWas',
                        array('ANSWER' => $show_answer)
                    );

                    $html[] = '<td>' . $show_answer . '</td>';

                    if (AnswerFeedbackDisplay::allowed(
                        $configuration,
                        $this->get_complex_content_object_question(),
                        true,
                        true
                    )
                    )
                    {
                        $comment = $best_answer->has_comment() ? $best_answer->get_comment() : '-';

                        $html[] = '<td>' . $comment . '</td>';
                    }

                    if ($configuration->show_score())
                    {
                        $html[] = '<td></td>';
                    }

                    $html[] = '</tr>';
                }
                break;
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Helper function to retrieve translations in the assessment namespace
     *
     * @param string $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getAssessmentTranslation($variable, $parameters = null)
    {
        return Translation::getInstance()->getTranslation(
            $variable, $parameters, 'Chamilo\Core\Repository\ContentObject\Assessment'
        );
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
