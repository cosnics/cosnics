<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariable extends ConditionVariable
{

    /**
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    private array $conditionVariables;

    /**
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function __construct(array $conditionVariables = [])
    {
        $this->conditionVariables = $conditionVariables;
    }

    public function add(ConditionVariable $conditionVariable): DistinctConditionVariable
    {
        $this->conditionVariables[] = $conditionVariable;

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function get(): array
    {
        return $this->conditionVariables;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $variableParts = [];

        foreach ($this->get() as $conditionVariable)
        {
            $variableParts[] = $conditionVariable->getHashParts();
        }

        $hashParts[] = $variableParts;

        return $hashParts;
    }

    public function hasConditionVariables(): bool
    {
        return count($this->get()) > 0;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function set(array $conditionVariables): DistinctConditionVariable
    {
        $this->conditionVariables = $conditionVariables;

        return $this;
    }
}
