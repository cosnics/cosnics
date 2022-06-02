<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A ConditionVariable that describes an operation on two other ConditionVariables
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OperationConditionVariable extends ConditionVariable
{

    public const ADDITION = 1;
    public const BITWISE_AND = 5;
    public const BITWISE_OR = 6;
    public const DIVISION = 4;
    public const MINUS = 2;
    public const MULTIPLICATION = 3;

    private ConditionVariable $leftConditionVariable;

    private int $operator;

    private ConditionVariable $rightConditionVariable;

    public function __construct(
        ConditionVariable $leftConditionVariable, int $operator, ConditionVariable $rightConditionVariable
    )
    {
        $this->leftConditionVariable = $leftConditionVariable;
        $this->operator = $operator;
        $this->rightConditionVariable = $rightConditionVariable;
    }

    public function getHashParts(): array
    {
        $hashParts = ConditionVariable::getHashParts();

        $parts = [];
        $parts[] = $this->getLeftConditionVariable()->getHashParts();
        $parts[] = $this->getRightConditionVariable()->getHashParts();

        if ($this->getOperator() != self::DIVISION)
        {
            sort($parts);
        }

        foreach ($parts as $part)
        {
            $hashParts[] = $part;
        }

        $hashParts[] = $this->getOperator();

        return $hashParts;
    }

    public function getLeftConditionVariable(): ConditionVariable
    {
        return $this->leftConditionVariable;
    }

    public function setLeftConditionVariable(ConditionVariable $leftConditionVariable): OperationConditionVariable
    {
        $this->leftConditionVariable = $leftConditionVariable;

        return $this;
    }

    public function getOperator(): int
    {
        return $this->operator;
    }

    public function setOperator(int $operator): OperationConditionVariable
    {
        $this->operator = $operator;

        return $this;
    }

    public function getRightConditionVariable(): ConditionVariable
    {
        return $this->rightConditionVariable;
    }

    public function setRightConditionVariable(ConditionVariable $rightConditionVariable): OperationConditionVariable
    {
        $this->rightConditionVariable = $rightConditionVariable;

        return $this;
    }
}
