<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

/**
 * This class represents a selection condition that requires an equality.
 * An example of an instance would be a condition
 * that requires that the id of a DataClass be the number 4.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @package common.libraries
 */
class EqualityCondition extends ComparisonCondition
{

    /**
     * Constructor
     *
     * @param $name string
     * @param $value string
     * @param $storage_unit string
     * @param $is_alias boolean
     */
    public function __construct($name, $value, $storage_unit = null, $is_alias = false)
    {
        parent :: __construct($name, self :: EQUAL, $value, $storage_unit, $is_alias);
    }
}
