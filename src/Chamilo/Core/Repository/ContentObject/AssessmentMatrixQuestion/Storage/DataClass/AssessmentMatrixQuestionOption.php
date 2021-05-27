<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass;

/**
 *
 * @package repository.lib.content_object.matrix_question
 */

/**
 * This class represents an option in a matrix question.
 */
class AssessmentMatrixQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_MATCHES = 'matches';

    private $value;

    private $score;

    private $feedback;

    private $matches;

    /**
     * Creates a new option for a matrix question
     *
     * @param string $value The value of the option
     * @param int $match The index of the match corresponding to this option
     * @param int $score The score of this answer in the question
     */
    public function __construct($value = '', $matches = [], $score = 1, $feedback = '')
    {
        $this->value = $value;
        $this->score = $score;
        $this->feedback = $feedback;
        $this->matches = $matches;
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
    public function get_matches()
    {
        return unserialize($this->matches);
    }

    public function get_serialized_matches()
    {
        return $this->matches;
    }

    public function set_feedback($value)
    {
        $this->feedback = $value;
    }

    public function set_value($value)
    {
        $this->value = $value;
    }
}
