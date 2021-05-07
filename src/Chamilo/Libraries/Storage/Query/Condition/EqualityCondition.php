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

    /**
     * Constructor
     *
     * @param $name string|ConditionVariable
     * @param $value string|ConditionVariable
     * @param $storageUnit string
     * @param $isAlias boolean
     */
    public function __construct($name, $value, $storageUnit = null, $isAlias = false)
    {
        parent::__construct($name, self::EQUAL, $value, $storageUnit, $isAlias);
    }
}
