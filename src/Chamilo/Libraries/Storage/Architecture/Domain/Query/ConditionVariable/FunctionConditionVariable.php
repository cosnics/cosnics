<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Domain\Enum\FunctionTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\FunctionConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FunctionConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    private ?string $alias;

    private ConditionVariableInterface $conditionVariable;

    private FunctionTypeEnum $function;

    public function __construct(
        FunctionTypeEnum $function, ConditionVariableInterface $conditionVariable, ?string $alias = null
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->function = $function;
        $this->alias = $alias;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getConditionVariable(): ConditionVariableInterface
    {
        return $this->conditionVariable;
    }

    public function setConditionVariable(ConditionVariableInterface $conditionVariable): FunctionConditionVariable
    {
        $this->conditionVariable = $conditionVariable;

        return $this;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\FunctionConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return FunctionConditionVariableTranslator::class;
    }

    public function getFunction(): FunctionTypeEnum
    {
        return $this->function;
    }

    public function setFunction(FunctionTypeEnum $function): static
    {
        $this->function = $function;

        return $this;
    }

    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getConditionVariable()->getHashParts(),
            $this->getFunction()->value,
            $this->getAlias()
        ];
    }
}
