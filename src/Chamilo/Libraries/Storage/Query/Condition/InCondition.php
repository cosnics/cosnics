<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a selection condition that requires a value to be present in a list of values. An example of an
 * instance would be a condition that requires that the id of a DataClass object be contained in the list {4,10,12}.
 *
 * @author Bart Mollet
 * @author Hans De Bisschop
 * @package common.libraries
 */
class InCondition extends Condition
{

    /**
     * Gets the DataClass property
     *
     * @var string
     */
    private $name;

    /**
     * The list of values that defines the selection
     *
     * @var string
     */
    private $values;

    /**
     * Gets the storage unit of the DataClass
     *
     * @var string
     */
    private $storage_unit;

    /**
     * Is the storage unit name already an alias?
     *
     * @var boolean
     */
    private $is_alias;

    /**
     *
     * @param $name string
     * @param $values multitype:string
     * @param $storage_unit string
     * @param $is_alias boolean
     */
    public function __construct($name, $values, $storage_unit = null, $is_alias = false)
    {
        $this->name = $name;
        $this->values = $values;
        $this->storage_unit = $storage_unit;
        $this->is_alias = $is_alias;
    }

    /**
     * Gets the DataClass property
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Gets the list of values that defines the selection
     *
     * @return string
     */
    public function get_values()
    {
        return $this->values;
    }

    /**
     * Gets the storage unit of the DataClass
     *
     * @return string
     */
    public function get_storage_unit()
    {
        return $this->storage_unit;
    }

    /**
     * Is the storage unit already an alias?
     *
     * @return boolean
     */
    public function is_alias()
    {
        return $this->is_alias;
    }

    public function hash()
    {
        if (! $this->get_hash())
        {
            $hashes = array();

            $hashes[] = $this->name instanceof ConditionVariable ? $this->name->hash() : $this->name;
            ksort($this->values);
            $hashes[] = $this->values;
            $hashes[] = $this->storage_unit;
            $hashes[] = $this->is_alias;

            $this->set_hash(parent :: hash($hashes));
        }

        return $this->get_hash();
    }
}
