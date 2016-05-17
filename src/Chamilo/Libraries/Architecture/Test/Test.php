<?php
namespace Chamilo\Libraries\Architecture\Test;

/**
 * This abstract test case is used as a base for all chamilo tests
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Test extends \PHPUnit_Framework_TestCase
{

    protected $backupGlobalsBlacklist = array('_MDB2_databases');

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns a reflection method for the tests of protected / private methods
     * 
     * @param string $class_name
     * @param string $method_name
     *
     * @return \ReflectionMethod
     */
    protected function get_method($class_name, $method_name)
    {
        $reflection_class = new \ReflectionClass($class_name);
        
        $reflection_method = $reflection_class->getMethod($method_name);
        $reflection_method->setAccessible(true);
        
        return $reflection_method;
    }

    /**
     * Returns a reflection property for the usage of the protected / private properties
     * 
     * @param string $class_name
     * @param string $property
     */
    protected function get_property($class_name, $property_name)
    {
        $reflection_class = new \ReflectionClass($class_name);
        
        $reflection_property = $reflection_class->getProperty($property_name);
        $reflection_property->setAccessible(true);
        
        return $reflection_property;
    }

    /**
     * Returns the value of a protected / private property of a class with use of reflection
     * 
     * @param mixed $object
     * @param string $property_name
     *
     * @return mixed
     */
    protected function get_property_value($object, $property_name)
    {
        $reflection_property = $this->get_property(get_class($object), $property_name);
        
        return $reflection_property->getValue($object);
    }

    /**
     * Sets the value of a protected / private property of a class with use of reflection
     *
     * @param mixed $object
     * @param string $property_name
     * @param mixed $value
     *
     * @return mixed
     */
    protected function set_property_value($object, $property_name, $value)
    {
        $reflection_property = $this->get_property(get_class($object), $property_name);

        return $reflection_property->setValue($object, $value);
    }

    /**
     * Counts the constants of a class with optionally a given prefix
     * 
     * @param DataClass $object
     * @param string $prefix - [OPTIONAL]
     */
    protected function count_constants($object, $prefix = null)
    {
        return count($this->get_constants($object, $prefix));
    }

    /**
     * Retrieves the constants of a class with optionally a given prefix
     * 
     * @param DataClass $object
     * @param string $prefix - [OPTIONAL]
     */
    protected function get_constants($object, $prefix)
    {
        $returned_constants = array();
        
        $reflection_class = new \ReflectionClass($object);
        $constants = $reflection_class->getConstants();
        
        foreach ($constants as $constant_name => $constant_value)
        {
            if (strpos($constant_name, $prefix) === 0)
            {
                $returned_constants[$constant_name] = $constant_value;
            }
        }
        
        return $returned_constants;
    }
}
