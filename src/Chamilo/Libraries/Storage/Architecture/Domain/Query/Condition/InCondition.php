<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition;

use Chamilo\Libraries\Protocol\Security\Architecture\Trait\HashableTrait;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableInterface;
use Chamilo\Libraries\Storage\Service\Condition\InConditionTranslator;

/**
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition
 */
class InCondition implements ConditionInterface
{
    use HashableTrait;

    private ConditionVariableInterface $conditionVariable;

    private array $values;

    public function __construct(
        ConditionVariableInterface $conditionVariable, array $values
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->values = $values;
    }

    public function getConditionTranslatorClass(): string
    {
        return InConditionTranslator::class;
    }

    public function getConditionVariable(): ConditionVariableInterface
    {
        return $this->conditionVariable;
    }

    public function getHashParts(): array
    {
        $hashParts = [];

        $hashParts[] = static::class;
        $hashParts[] = $this->getConditionVariable()->getHashParts();

        $values = $this->getValues();

        ksort($values);
        $hashParts[] = $values;

        return $hashParts;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
