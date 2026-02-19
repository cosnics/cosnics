<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\ComparisonTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\Condition\ComparisonConditionTranslator;

/**
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
class ComparisonCondition implements ConditionInterface
{
    use HashableTrait;

    private ConditionVariableInterface $leftConditionVariable;

    private ComparisonTypeEnum $operator;

    private ?ConditionVariableInterface $rightConditionVariable;

    public function __construct(
        ConditionVariableInterface $leftConditionVariable, ComparisonTypeEnum $operator,
        ?ConditionVariableInterface $rightConditionVariable
    )
    {
        $this->leftConditionVariable = $leftConditionVariable;
        $this->operator = $operator;
        $this->rightConditionVariable = $rightConditionVariable;
    }

    public function getConditionTranslatorClass(): string
    {
        return ComparisonConditionTranslator::class;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;
        $hashParts[] = $this->getOperator()->value;

        switch ($this->getOperator()) {
            case ComparisonTypeEnum::LESS_THAN :
            case ComparisonTypeEnum::LESS_THAN_OR_EQUAL :
                $hashParts[] = $this->getRightConditionVariable() instanceof ConditionVariableInterface ?
                    $this->getRightConditionVariable()->getHashParts() : null;
                $hashParts[] = $this->getLeftConditionVariable()->getHashParts();
                break;
            case ComparisonTypeEnum::EQUAL :
                $parts = [];
                $parts[] = $this->getLeftConditionVariable()->getHashParts();
                $parts[] = $this->getRightConditionVariable() instanceof ConditionVariableInterface ?
                    $this->getRightConditionVariable()->getHashParts() : null;

                sort($parts);

                foreach ($parts as $part) {
                    $hashParts[] = $part;
                }

                break;
            default :
                $hashParts[] = $this->getLeftConditionVariable()->getHashParts();
                $hashParts[] = $this->getRightConditionVariable() instanceof ConditionVariableInterface ?
                    $this->getRightConditionVariable()->getHashParts() : null;
                break;
        }

        return $hashParts;
    }

    public function getLeftConditionVariable(): ConditionVariableInterface
    {
        return $this->leftConditionVariable;
    }

    public function getOperator(): ComparisonTypeEnum
    {
        return $this->operator;
    }

    public function getRightConditionVariable(): ?ConditionVariableInterface
    {
        return $this->rightConditionVariable;
    }
}
