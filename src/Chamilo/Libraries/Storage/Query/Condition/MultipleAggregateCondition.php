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
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    private array $conditions;

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition[] $conditions
     */
    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getOperator();

        $aggregateParts = [];

        foreach ($this->getConditions() as $condition)
        {
            $aggregateParts[] = $condition->getHashParts();
        }

        sort($aggregateParts);

        $hashParts[] = $aggregateParts;

        return $hashParts;
    }

    abstract public function getOperator(): string;
}
