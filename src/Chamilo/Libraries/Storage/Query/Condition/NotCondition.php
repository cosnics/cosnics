<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This type of aggregate condition negates a single condition, thus requiring that that condition not be met.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class NotCondition extends AggregateCondition
{

    /**
     * The condition to negate
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $condition;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public function __construct(Condition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Gets the condition to negate
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->get_condition()->getHashParts();

        return $hashParts;
    }
}
