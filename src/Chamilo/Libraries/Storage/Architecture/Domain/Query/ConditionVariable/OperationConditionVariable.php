<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\OperationTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\OperationConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class OperationConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    private ConditionVariableInterface $leftConditionVariable;

    private OperationTypeEnum $operator;

    private ConditionVariableInterface $rightConditionVariable;

    public function __construct(
        ConditionVariableInterface $leftConditionVariable, OperationTypeEnum $operator,
        ConditionVariableInterface $rightConditionVariable
    )
    {
        $this->leftConditionVariable = $leftConditionVariable;
        $this->operator = $operator;
        $this->rightConditionVariable = $rightConditionVariable;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\OperationConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return OperationConditionVariableTranslator::class;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;

        $parts = [];
        $parts[] = $this->getLeftConditionVariable()->getHashParts();
        $parts[] = $this->getRightConditionVariable()->getHashParts();

        if ($this->getOperator() !== OperationTypeEnum::DIVISION) {
            sort($parts);
        }

        foreach ($parts as $part) {
            $hashParts[] = $part;
        }

        $hashParts[] = $this->getOperator()->value;

        return $hashParts;
    }

    public function getLeftConditionVariable(): ConditionVariableInterface
    {
        return $this->leftConditionVariable;
    }

    public function setLeftConditionVariable(ConditionVariableInterface $leftConditionVariable): static
    {
        $this->leftConditionVariable = $leftConditionVariable;

        return $this;
    }

    public function getOperator(): OperationTypeEnum
    {
        return $this->operator;
    }

    public function setOperator(OperationTypeEnum $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    public function getRightConditionVariable(): ConditionVariableInterface
    {
        return $this->rightConditionVariable;
    }

    public function setRightConditionVariable(ConditionVariableInterface $rightConditionVariable): static
    {
        $this->rightConditionVariable = $rightConditionVariable;

        return $this;
    }
}
