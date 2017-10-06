<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.multiple_choice_question
 */
/**
 * This class represents an option in a multiple choice question.
 */
class AssessmentMultipleChoiceQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_CORRECT = 'correct';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';

    private $value;
    // private $correct;
    private $score;

    private $feedback;

    public function __construct($value, $correct, $score, $feedback)
    {
        $this->value = $value;
        $this->correct = $correct;
        $this->score = $score;
        $this->feedback = $feedback;
    }

    public function get_feedback()
    {
        return $this->feedback;
    }

    public function has_feedback()
    {
        return StringUtilities::getInstance()->hasValue($this->get_feedback(), true);
    }

    public function get_score()
    {
        return $this->score;
    }

    public function is_correct()
    {
        return $this->correct;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function set_value($value)
    {
        $this->value = $value;
    }

    public function set_feedback($feedback)
    {
        $this->feedback = $feedback;
    }
}
