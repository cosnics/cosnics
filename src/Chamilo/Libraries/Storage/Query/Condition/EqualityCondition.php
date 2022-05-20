<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a selection condition that requires an equality.
 * An example of an instance would be a condition
 * that requires that the id of a DataClass be the number 4.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package Chamilo\Libraries\Storage\Query\Condition
 */
class EqualityCondition extends ComparisonCondition
{
    public function __construct(ConditionVariable $leftConditionVariable, ConditionVariable $rightConditionVariable)
    {
        parent::__construct($leftConditionVariable, self::EQUAL, $rightConditionVariable);
    }
}
