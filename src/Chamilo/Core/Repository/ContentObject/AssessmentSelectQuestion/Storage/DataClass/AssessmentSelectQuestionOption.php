<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.select_question
 */

/**
 * This class represents an option in a multiple choice question.
 */
class AssessmentSelectQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_CORRECT = 'correct';

    private $value;

    private $correct;

    private $score;

    private $feedback;

    /**
     * Creates a new option for a multiple choice question
     *
     * @param int $score The score of this answer in the question
     * @param string $feedback The feedback of this answer in the question
     */
    public function __construct($value, $correct, $score, $feedback)
    {
        $this->value = $value;
        $this->correct = $correct;
        $this->score = $score;
        $this->feedback = $feedback;
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

    public function get_feedback()
    {
        return $this->feedback;
    }

    public function set_feedback($feedback)
    {
        $this->feedback = $feedback;
    }

    /**
     * Gets the weight of this answer
     */
    public function get_score()
    {
        return $this->score;
    }

    /**
     * Determines if this option is a correct answer
     *
     * @return boolean
     */
    public function is_correct()
    {
        return $this->correct;
    }

    public function has_feedback()
    {
        return StringUtilities::getInstance()->hasValue($this->get_feedback(), true);
    }
}
