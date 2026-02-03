<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;

/**
 * This class represents a condition that consists of multiple aggregated conditions.
 * Thus, it is used to model a single
 * relationship (AND, OR and perhaps others) between its aggregated conditions.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
abstract class MultipleAggregateCondition
{
    use HashableTrait;

    /**
     * @var \Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface[]
     */
    private array $conditions;

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface[] $conditions
     */
    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array<\Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface>
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;
        $hashParts[] = $this->getOperator();

        $aggregateParts = [];

        foreach ($this->getConditions() as $condition) {
            $aggregateParts[] = $condition->getHashParts();
        }

        sort($aggregateParts);

        $hashParts[] = $aggregateParts;

        return $hashParts;
    }

    abstract public function getOperator(): string;
}
