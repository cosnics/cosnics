<?php
namespace Chamilo\Libraries\Storage\DataClass\ForeignObject;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the parameters to retrieve (a) foreign object(s) with lazy loading.
 * 
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ForeignObjectsParameters
{

    /**
     * **************************************************************************************************************
     * Properties
     * **************************************************************************************************************
     */
    
    /**
     * The base dataclass object for which we want to retrieve the foreign objects
     * 
     * @var DataClass
     */
    private $base_object;

    /**
     * The classname for the foreign object
     * 
     * @var string
     */
    private $foreign_class;

    /**
     * The foreign key property
     * 
     * @var string
     */
    private $foreign_key;

    /**
     * **************************************************************************************************************
     * Main Functionality
     * **************************************************************************************************************
     */
    public function __construct($base_object, $foreign_class, $foreign_key = null)
    {
        $this->set_base_object($base_object);
        $this->set_foreign_class($foreign_class);
        $this->set_foreign_key($foreign_key);
    }

    /**
     * Returns the condition for the foreign objects retrieval
     * 
     * @return Condition
     */
    public function get_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable($this->get_foreign_class(), DataClass::PROPERTY_ID), 
            new StaticConditionVariable($this->get_base_object()->get_default_property($this->get_foreign_key())));
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality
     * **************************************************************************************************************
     */
    
    /**
     * Generates a key property with the given class name
     * 
     * @param string $class_name
     */
    protected function generate_key($class_name)
    {
        return $class_name::get_table_name() . '_' . DataClass::PROPERTY_ID;
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters
     * **************************************************************************************************************
     */
    
    /**
     * Returns the base object
     * 
     * @return DataClass
     */
    public function get_base_object()
    {
        return $this->base_object;
    }

    /**
     * Returns the classname for the foreign object
     * 
     * @return string
     */
    public function get_foreign_class()
    {
        return $this->foreign_class;
    }

    /**
     * Returns the foreign key property
     * 
     * @return string
     */
    public function get_foreign_key()
    {
        return $this->foreign_key;
    }

    /**
     * Sets the base object
     * 
     * @param DataClass $base_object
     */
    public function set_base_object(DataClass $base_object)
    {
        $this->base_object = $base_object;
    }

    /**
     * Sets the classname for the foreign object
     * 
     * @param string $foreign_class
     */
    public function set_foreign_class($foreign_class)
    {
        $this->foreign_class = $foreign_class;
    }

    /**
     * Sets the foreign key property
     * 
     * @param string $foreign_key
     */
    public function set_foreign_key($foreign_key)
    {
        if (is_null($foreign_key))
        {
            $foreign_key = $this->generate_key($this->get_foreign_class());
        }
        
        $this->foreign_key = $foreign_key;
    }
}
