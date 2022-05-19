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

    public function addConditionVariable(ConditionVariable $conditionVariable): DistinctConditionVariable
    {
        $this->conditionVariables[] = $conditionVariable;

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[]
     */
    public function getConditionVariables(): array
    {
        return $this->conditionVariables;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable[] $conditionVariables
     */
    public function setConditionVariables(array $conditionVariables): DistinctConditionVariable
    {
        $this->conditionVariables = $conditionVariables;

        return $this;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $variableParts = [];

        foreach ($this->getConditionVariables() as $conditionVariable)
        {
            $variableParts[] = $conditionVariable->getHashParts();
        }

        $hashParts[] = $variableParts;

        return $hashParts;
    }

    public function hasConditionVariables(): bool
    {
        return count($this->getConditionVariables()) > 0;
    }
}
