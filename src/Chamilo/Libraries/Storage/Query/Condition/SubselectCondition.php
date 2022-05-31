<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

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

    private PropertyConditionVariable $subselectConditionVariable;

    public function __construct(
        ConditionVariable $conditionVariable, PropertyConditionVariable $subselectConditionVariable,
        ?Condition $condition = null
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->subselectConditionVariable = $subselectConditionVariable;
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

    public function getSubselectConditionVariable(): PropertyConditionVariable
    {
        return $this->subselectConditionVariable;
    }
}
