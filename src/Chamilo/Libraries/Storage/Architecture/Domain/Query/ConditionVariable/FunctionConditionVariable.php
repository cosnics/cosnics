<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
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

    public const AVERAGE = 6;
    public const COUNT = 2;
    public const DISTINCT = 5;
    public const MAX = 4;
    public const MIN = 3;
    public const SUM = 1;

    private ?string $alias;

    private ConditionVariableInterface $conditionVariable;

    private int $function;

    public function __construct(int $function, ConditionVariableInterface $conditionVariable, ?string $alias = null)
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

    public function getFunction(): int
    {
        return $this->function;
    }

    public function setFunction(int $function): static
    {
        $this->function = $function;

        return $this;
    }

    public function getHashParts(): array
    {
        return [
            static::class,
            $this->getConditionVariable()->getHashParts(),
            $this->getFunction(),
            $this->getAlias()
        ];
    }
}
