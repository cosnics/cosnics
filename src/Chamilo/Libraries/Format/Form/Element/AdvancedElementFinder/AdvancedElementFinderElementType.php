<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

/**
 * class that describes a type for the advanced element finder
 */
class AdvancedElementFinderElementType
{
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_CONTEXT = 'application';
    const PROPERTY_METHOD = 'go';
    const PROPERTY_PARAMETERS = 'parameters';

    /**
     * Associative array for the properties
     * 
     * @var Array
     */
    private $properties;

    public function __construct($id, $name, $context, $method, $parameters = array())
    {
        $this->set_id($id);
        $this->set_name($name);
        $this->set_context($context);
        $this->set_method($method);
        $this->set_parameters($parameters);
    }

    /**
     * Sets a property in the associative array of properties
     * 
     * @param String $property_name
     * @param Object $value
     */
    public function set_property($property_name, $value)
    {
        $this->properties[$property_name] = $value;
    }

    /**
     * Retrieves a property from the associative array of properties
     * 
     * @param String $property_name
     */
    public function get_property($property_name)
    {
        return $this->properties[$property_name];
    }

    /**
     * Returns the id of this element type
     * 
     * @return int
     */
    public function get_id()
    {
        return $this->get_property(self::PROPERTY_ID);
    }

    /**
     * Sets the id of this element type
     * 
     * @param int $id
     */
    public function set_id($id)
    {
        $this->set_property(self::PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this element type
     * 
     * @return String
     */
    public function get_name()
    {
        return $this->get_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name of this element
     * 
     * @param String name
     */
    public function set_name($name)
    {
        $this->set_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the context of this element
     * 
     * @param String context
     */
    public function set_context($context)
    {
        $this->set_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the method of this element
     * 
     * @param String method
     */
    public function set_method($method)
    {
        $this->set_property(self::PROPERTY_METHOD, $method);
    }

    /**
     * Sets the parameters of this element
     * 
     * @param Array $parameters
     */
    public function set_parameters($parameters)
    {
        $this->set_property(self::PROPERTY_PARAMETERS, $parameters);
    }

    /**
     * Returns this element as an array
     * 
     * @return Array
     */
    public function as_array()
    {
        return $this->properties;
    }
}
