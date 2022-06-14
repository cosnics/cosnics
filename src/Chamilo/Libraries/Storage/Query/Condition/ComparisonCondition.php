<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a condition that requires an inequality.
 * An example would be requiring that a number be greater
 * than 4.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class ComparisonCondition extends Condition
{

    public const EQUAL = 5;
    public const GREATER_THAN = 3;
    public const GREATER_THAN_OR_EQUAL = 4;
    public const LESS_THAN = 1;
    public const LESS_THAN_OR_EQUAL = 2;

    private ConditionVariable $leftConditionVariable;

    private int $operator;

    private ?ConditionVariable $rightConditionVariable;

    public function __construct(
        ConditionVariable $leftConditionVariable, int $operator, ?ConditionVariable $rightConditionVariable
    )
    {
        $this->leftConditionVariable = $leftConditionVariable;
        $this->operator = $operator;
        $this->rightConditionVariable = $rightConditionVariable;
    }

    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getOperator();

        switch ($this->getOperator())
        {
            case self::LESS_THAN :
            case self::LESS_THAN_OR_EQUAL :
                $hashParts[] = $this->getRightConditionVariable() instanceof ConditionVariable ?
                    $this->getRightConditionVariable()->getHashParts() : $this->getRightConditionVariable();
                $hashParts[] = $this->getLeftConditionVariable()->getHashParts();
                break;
            case self::EQUAL :
                $parts = [];
                $hashParts[] = $this->getLeftConditionVariable()->getHashParts();
                $hashParts[] = $this->getRightConditionVariable() instanceof ConditionVariable ?
                    $this->getRightConditionVariable()->getHashParts() : $this->getRightConditionVariable();

                sort($parts);

                foreach ($parts as $part)
                {
                    $hashParts[] = $part;
                }

                break;
            default :
                $hashParts[] = $this->getLeftConditionVariable()->getHashParts();
                $hashParts[] = $this->getRightConditionVariable() instanceof ConditionVariable ?
                    $this->getRightConditionVariable()->getHashParts() : $this->getRightConditionVariable();
                break;
        }

        return $hashParts;
    }

    public function getLeftConditionVariable(): ConditionVariable
    {
        return $this->leftConditionVariable;
    }

    public function getOperator(): int
    {
        return $this->operator;
    }

    public function getRightConditionVariable(): ?ConditionVariable
    {
        return $this->rightConditionVariable;
    }

}
