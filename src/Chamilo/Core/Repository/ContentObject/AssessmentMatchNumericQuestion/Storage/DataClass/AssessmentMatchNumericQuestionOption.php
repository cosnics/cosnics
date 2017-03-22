<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib.content_object.match_numeric_question
 */

/**
 * This class represents an option in a matching question.
 */
class AssessmentMatchNumericQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_TOLERANCE = 'tolerance';

    private $value;

    private $tolerance;

    private $score;

    private $feedback;

    public function __construct($value, $tolerance, $score, $feedback)
    {
        $this->value = $value;
        $this->tolerance = $tolerance;
        $this->score = $score;
        $this->feedback = $feedback;
    }

    public function get_value()
    {
        return $this->value;
    }

    public function set_value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function get_tolerance()
    {
        return $this->tolerance;
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

    public function matches($answer, $tolerance_type)
    {
        $answer = floatval(str_replace(',', '.', $answer));
        $value = floatval(str_replace(',', '.', $this->get_value()));
        $tolerance = floatval(str_replace(',', '.', $this->get_tolerance()));
        
        switch ($tolerance_type)
        {
            case AssessmentMatchNumericQuestion::TOLERANCE_TYPE_ABSOLUTE :
                $min = $value - abs($tolerance);
                $max = $value + abs($tolerance);
                return $min <= $answer && $answer <= $max;
            
            case AssessmentMatchNumericQuestion::TOLERANCE_TYPE_RELATIVE :
                $min = $value - abs($value * $tolerance / 100);
                $max = $value + abs($value * $tolerance / 100);
                return $min <= $answer && $answer <= $max;
            
            default :
                throw new \Exception('Unknown tolerance type');
        }
    }
}
