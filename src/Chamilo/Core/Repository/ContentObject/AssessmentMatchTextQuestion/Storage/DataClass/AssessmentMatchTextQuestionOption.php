<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass;

/**
 *
 * @package repository.lib.content_object.match_text_question
 */
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class represents an option in a match tex question.
 */
class AssessmentMatchTextQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';

    private $value;

    private $score;

    private $feedback;

    public function __construct($value, $score, $feedback)
    {
        $this->value = $value;
        $this->score = $score;
        $this->feedback = $feedback;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function get_score()
    {
        return $this->score;
    }

    public function get_feedback()
    {
        return $this->feedback;
    }

    public function has_feedback()
    {
        return StringUtilities::getInstance()->hasValue($this->get_feedback(), true);
    }

    public function set_feedback($value)
    {
        $this->feedback = $value;
    }

    public function matches($answer, $ignore_case, $use_wildcards)
    {
        $answer = trim($answer);
        $optionValue = trim($this->get_value());

        if ($use_wildcards)
        {
            $star = '__star__';
            $value = str_replace('*', $star, $optionValue);
            $value = preg_quote($value);
            $value = str_replace($star, '.*', $value);
            $value = "/$value/" . ($ignore_case ? 'i' : '');
            return preg_match($value, $answer) > 0;
        }
        else
        {
            if ($ignore_case)
            {
                return strtolower($answer) == strtolower($optionValue);
            }
            else
            {
                return $answer == $optionValue;
            }
        }
    }
}
