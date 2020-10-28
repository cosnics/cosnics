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

    /**
     * The DataClass property
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    private $name;

    /**
     * The storage unit of the DataClass
     *
     * @var string
     */
    private $storage_unit_name;

    /**
     * The DataClass property of the object used in the subselect
     *
     * @var  \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    private $value;

    /**
     * The storage unit of the DataClass used in the subselect
     *
     * @var string
     */
    private $storage_unit_value;

    /**
     * The condition for the subselect
     *
     * @var \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    private $condition;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $name
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $value
     * @param string $storageUnitValue
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param $storageUnitName string
     */
    public function __construct(
        $name, $value, $storageUnitValue, $condition = null, $storageUnitName = null
    )
    {
        $this->name = $name;
        $this->value = $value;
        $this->storage_unit_value = $storageUnitValue;
        $this->storage_unit_name = $storageUnitName;
        $this->condition = $condition;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();

        $hashParts[] =
            $this->get_name() instanceof ConditionVariable ? $this->get_name()->getHashParts() : $this->get_name();
        $hashParts[] =
            $this->get_value() instanceof ConditionVariable ? $this->get_value()->getHashParts() : $this->get_value();
        $hashParts[] = $this->get_storage_unit_value();
        $hashParts[] = $this->get_storage_unit_name();

        if ($this->get_condition() instanceof Condition)
        {
            $hashParts[] = $this->get_condition()->getHashParts();
        }
        else
        {
            $hashParts[] = null;
        }

        return $hashParts;
    }

    /**
     * Gets the condition for the subselect
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     * Gets the DataClass property
     *
     * @return  \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
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
    public function get_storage_unit_name()
    {
        return $this->storage_unit_name;
    }

    /**
     * Gets the storage unit of the DataClass used in the subselect
     *
     * @return string
     */
    public function get_storage_unit_value()
    {
        return $this->storage_unit_value;
    }

    /**
     * Gets the DataClass property of the object used in the subselect
     *
     * @return  \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    public function get_value()
    {
        return $this->value;
    }
}
