<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass;

/**
 *
 * @package repository.lib.content_object.matching_question
 */

/**
 * This class represents an option in a matching question.
 */
class AssessmentMatchingQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_MATCH = 'match';

    private $value;

    private $score;

    private $feedback;

    private $match;

    /**
     * Creates a new option for a matching question
     *
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $score The score of this answer in the question
     */
    public function __construct($value, $match, $score, $feedback)
    {
        $this->value = $value;
        $this->score = $score;
        $this->feedback = $feedback;
        $this->match = $match;
    }

    /**
     * Gets the value of this option
     *
     * @return string
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Gets the score of this answer
     */
    public function get_score()
    {
        return $this->score;
    }

    public function get_feedback()
    {
        return $this->feedback;
    }

    /**
     * Gets the index of the match corresponding to this option
     *
     * @return int
     */
    public function get_match()
    {
        return $this->match;
    }

    public function set_value($value)
    {
        $this->value = $value;
    }

    public function set_feedback($value)
    {
        $this->feedback = $value;
    }
}
