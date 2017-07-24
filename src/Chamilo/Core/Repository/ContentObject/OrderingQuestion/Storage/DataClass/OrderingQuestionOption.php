<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass;

/**
 * $Id: ordering_question_option.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.ordering_question
 */
/**
 * This class represents an option in a ordering question.
 */
class OrderingQuestionOption
{
    const PROPERTY_VALUE = 'value';
    const PROPERTY_ORDER = 'order';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_FEEDBACK = 'feedback';

    /**
     * The value of the option
     */
    private $value;

    /**
     * The order of the option
     */
    private $order;

    private $score;

    private $feedback;

    /**
     * Creates a new option for a ordering question
     * 
     * @param string $value The value of the option
     * @param int $rank The rank of this answer in the question
     */
    public function __construct($value, $order, $score, $feedback)
    {
        $this->value = $value;
        $this->order = $order;
        $this->score = $score;
        $this->feedback = $feedback;
    }

    /**
     * Gets the order of this option
     * 
     * @return int
     */
    public function get_order()
    {
        return $this->order;
    }

    public function set_order($order)
    {
        $this->order = $order;
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

    public function set_value($value)
    {
        $this->value = $value;
    }

    /**
     *
     * @return field_type
     */
    public function get_score()
    {
        return $this->score;
    }

    /**
     *
     * @param field_type $score
     */
    public function set_score($score)
    {
        $this->score = $score;
    }

    /**
     *
     * @return field_type
     */
    public function get_feedback()
    {
        return $this->feedback;
    }

    /**
     *
     * @param string $feedback
     */
    public function set_feedback($feedback)
    {
        $this->feedback = $feedback;
    }
}
