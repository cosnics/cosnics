<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\ConditionVariable\DistinctConditionVariableTranslator;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DistinctConditionVariable implements ConditionVariableInterface
{
    use HashableTrait;

    /**
     * @var \Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface[]
     */
    private array $conditionVariables;

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface[] $conditionVariables
     */
    public function __construct(array $conditionVariables = [])
    {
        $this->conditionVariables = $conditionVariables;
    }

    public function add(ConditionVariableInterface $conditionVariable): static
    {
        $this->conditionVariables[] = $conditionVariable;

        return $this;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface[]
     */
    public function get(): array
    {
        return $this->conditionVariables;
    }

    /**
     * @return class-string<\Chamilo\Libraries\Storage\Service\ConditionVariable\DistinctConditionVariableTranslator>
     */
    public function getConditionVariableTranslatorClass(): string
    {
        return DistinctConditionVariableTranslator::class;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;

        $variableParts = [];

        foreach ($this->get() as $conditionVariable) {
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
     * @param \Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface[] $conditionVariables
     */
    public function set(array $conditionVariables): static
    {
        $this->conditionVariables = $conditionVariables;

        return $this;
    }
}
