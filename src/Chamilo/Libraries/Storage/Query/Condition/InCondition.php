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

    private array $values;

    public function __construct(
        ConditionVariable $conditionVariable, array $values
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->values = $values;
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

        return $hashParts;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
