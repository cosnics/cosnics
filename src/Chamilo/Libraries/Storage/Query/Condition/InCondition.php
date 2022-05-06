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

    /**
     * Is the storage unit name already an alias?
     *
     * @var boolean
     */
    private $is_alias;

    /**
     * Gets the DataClass property
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    private $name;

    /**
     * Gets the storage unit of the DataClass
     *
     * @var string
     */
    private $storage_unit;

    /**
     * The list of values that defines the selection
     *
     * @var string[]
     */
    private $values;

    /**
     *
     * @param $name string|ConditionVariable
     * @param $values array
     * @param $storageUnit string
     * @param $isAlias boolean
     */
    public function __construct($name, $values, $storageUnit = null, $isAlias = false)
    {
        $this->name = $name;
        $this->values = $values;
        $this->storage_unit = $storageUnit;
        $this->is_alias = $isAlias;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts(): array
    {
        $hashParts = parent::getHashParts();

        $hashParts[] =
            $this->get_name() instanceof ConditionVariable ? $this->get_name()->getHashParts() : $this->get_name();

        $values = $this->get_values();

        ksort($values);
        $hashParts[] = $values;

        $hashParts[] = $this->get_storage_unit();
        $hashParts[] = $this->is_alias();

        return $hashParts;
    }

    /**
     * Gets the DataClass property
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function get_name()
    {
        return $this->name;
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
     * Gets the list of values that defines the selection
     *
     * @return string[]
     */
    public function get_values()
    {
        return $this->values;
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
}
