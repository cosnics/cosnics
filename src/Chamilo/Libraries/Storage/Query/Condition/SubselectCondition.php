<?php
namespace Chamilo\Libraries\Storage\Query\Condition;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * This class represents a subselect condition which allows you to pass the result of a specific query to an in
 * condition in the parent query
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 * @package common.libraries
 */
class SubselectCondition extends Condition
{

    /**
     * The DataClass property
     * 
     * @var string
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
     * @var string
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
     * @var \libraries\storage\Condition
     */
    private $condition;

    /**
     * An optional DataManager used in case subselect refers to a different context
     * 
     * @var \libraries\storage\data_manager\DataManager
     */
    private $data_manager;

    /**
     * Constructor
     * 
     * @param $name string
     * @param $value string
     * @param $storage_unit_value string
     * @param $condition \libraries\storage\Condition
     * @param $storage_unit_name string
     * @param $data_manager \libraries\storage\data_manager\DataManager
     */
    public function __construct($name, $value, $storage_unit_value, $condition = null, $storage_unit_name = null, 
        $data_manager = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->storage_unit_value = $storage_unit_value;
        $this->storage_unit_name = $storage_unit_name;
        $this->condition = $condition;
        $this->data_manager = $data_manager;
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
     * Gets the DataClass property of the object used in the subselect
     * 
     * @return string
     */
    public function get_value()
    {
        return $this->value;
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
     * Gets the storage unit of the DataClass
     * 
     * @return string
     */
    public function get_storage_unit_name()
    {
        return $this->storage_unit_name;
    }

    /**
     * Gets the condition for the subselect
     */
    public function get_condition()
    {
        return $this->condition;
    }

    /**
     * Gets the optional DataManager used in case subselect refers to a different context
     */
    public function get_data_manager()
    {
        return $this->data_manager;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\Condition\Condition::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = parent::getHashParts();
        
        $hashParts[] = $this->get_name() instanceof ConditionVariable ? $this->get_name()->getHashParts() : $this->get_name();
        $hashParts[] = $this->get_value() instanceof ConditionVariable ? $this->get_value()->getHashParts() : $this->get_value();
        $hashParts[] = $this->get_storage_unit_value();
        $hashParts[] = $this->get_storage_unit_name();
        $hashParts[] = ($this->get_data_manager() ? $this->get_data_manager()->class_name() : null);
        
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
}
