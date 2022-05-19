<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a subselect condition which allows you to pass the result of a specific query to an in
 * condition in the parent query
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class SubselectCondition extends Condition
{

    private ?Condition $condition;

    private ConditionVariable $conditionVariable;

    private ?string $storageUnitName;

    private ConditionVariable $subselectConditionVariable;

    private string $subselectStorageUnitName;

    public function __construct(
        ConditionVariable $conditionVariable, ConditionVariable $subselectConditionVariable,
        string $subselectStorageUnitName, ?Condition $condition = null, ?string $storageUnitName = null
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->subselectConditionVariable = $subselectConditionVariable;
        $this->subselectStorageUnitName = $subselectStorageUnitName;
        $this->storageUnitName = $storageUnitName;
        $this->condition = $condition;
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function getConditionVariable(): ConditionVariable
    {
        return $this->conditionVariable;
    }

    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] = $this->getConditionVariable()->getHashParts();
        $hashParts[] = $this->getSubselectConditionVariable()->getHashParts();
        $hashParts[] = $this->getSubselectStorageUnitName();
        $hashParts[] = $this->getStorageUnitName();

        if ($this->getCondition() instanceof Condition)
        {
            $hashParts[] = $this->getCondition()->getHashParts();
        }
        else
        {
            $hashParts[] = null;
        }

        return $hashParts;
    }

    public function getStorageUnitName(): ?string
    {
        return $this->storageUnitName;
    }

    public function getSubselectConditionVariable(): ConditionVariable
    {
        return $this->subselectConditionVariable;
    }

    public function getSubselectStorageUnitName(): string
    {
        return $this->subselectStorageUnitName;
    }
}
