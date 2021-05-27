<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This class represents a condition that consists of multiple aggregated conditions.
 * Thus, it is used to model a single
 * relationship (AND, OR and perhaps others) between its aggregated conditions.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
abstract class MultipleAggregateCondition extends AggregateCondition
{

    /**
     * The aggregated conditions
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    private $conditions;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition[] $conditions
     */
    public function __construct($conditions)
    {
        $this->conditions = (is_array($conditions) ? $conditions : func_get_args());
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->get_operator();

        $aggregateParts = [];

        foreach ($this->get_conditions() as $condition)
        {
            $aggregateParts[] = $condition->getHashParts();
        }

        sort($aggregateParts);

        $hashParts[] = $aggregateParts;

        return $hashParts;
    }

    /**
     * Gets the aggregated conditions
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    public function get_conditions()
    {
        return $this->conditions;
    }

    /**
     * Gets the operator of this MultipleAggregateCondition
     *
     * @return string
     */
    abstract public function get_operator();
}
