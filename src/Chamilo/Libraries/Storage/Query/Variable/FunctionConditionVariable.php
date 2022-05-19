<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes a function on another ConditionVariable
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariable extends ConditionVariable
{
    const AVERAGE = 6;
    const COUNT = 2;
    const DISTINCT = 5;
    const MAX = 4;
    const MIN = 3;
    const SUM = 1;

    private ?string $alias;

    private ConditionVariable $conditionVariable;

    private int $function;

    public function __construct(int $function, ConditionVariable $conditionVariable, ?string $alias = null)
    {
        $this->conditionVariable = $conditionVariable;
        $this->function = $function;
        $this->alias = $alias;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): ConditionVariable
    {
        $this->alias = $alias;

        return $this;
    }

    public function getConditionVariable(): ConditionVariable
    {
        return $this->conditionVariable;
    }

    public function setConditionVariable(ConditionVariable $conditionVariable)
    {
        $this->conditionVariable = $conditionVariable;

        return $this;
    }

    public function getFunction(): int
    {
        return $this->function;
    }

    public function setFunction(int $function): ConditionVariable
    {
        $this->function = $function;

        return $this;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $hashParts[] = $this->getConditionVariable()->getHashParts();
        $hashParts[] = $this->getFunction();
        $hashParts[] = $this->getAlias();

        return $hashParts;
    }
}
