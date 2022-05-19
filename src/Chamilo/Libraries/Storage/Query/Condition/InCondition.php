<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a selection condition that requires a value to be present in a list of values.
 * An example of an
 * instance would be a condition that requires that the id of a DataClass object be contained in the list {4,10,12}.
 *
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class InCondition extends Condition
{
    private ConditionVariable $conditionVariable;

    private bool $isAlias;

    private ?string $storageUnit;

    private array $values;

    public function __construct(
        ConditionVariable $conditionVariable, array $values, ?string $storageUnit = null, ?bool $isAlias = false
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->values = $values;
        $this->storageUnit = $storageUnit;
        $this->isAlias = $isAlias;
    }

    public function getConditionVariable(): ConditionVariable
    {
        return $this->conditionVariable;
    }

    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getConditionVariable()->getHashParts();

        $values = $this->getValues();

        ksort($values);
        $hashParts[] = $values;

        $hashParts[] = $this->getStorageUnit();
        $hashParts[] = $this->isAlias();

        return $hashParts;
    }

    public function getStorageUnit(): ?string
    {
        return $this->storageUnit;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function isAlias(): bool
    {
        return $this->isAlias;
    }
}
