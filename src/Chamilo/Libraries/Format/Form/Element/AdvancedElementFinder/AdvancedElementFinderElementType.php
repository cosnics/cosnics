<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

/**
 * class that describes a type for the advanced element finder
 *
 * @package Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder
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
     * @var string[]
     */
    private $properties;

    /**
     *
     * @param string $id
     * @param string $name
     * @param string $context
     * @param string $method
     * @param string[] $parameters
     */
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
     * @param string $propertyName
     * @param mixed $value
     */
    public function set_property($propertyName, $value)
    {
        $this->properties[$propertyName] = $value;
    }

    /**
     * Retrieves a property from the associative array of properties
     *
     * @param string $propertyName
     * @return mixed
     */
    public function get_property($propertyName)
    {
        return $this->properties[$propertyName];
    }

    /**
     * Returns the id of this element type
     *
     * @return string
     */
    public function get_id()
    {
        return $this->get_property(self::PROPERTY_ID);
    }

    /**
     * Sets the id of this element type
     *
     * @param string $id
     */
    public function set_id($id)
    {
        $this->set_property(self::PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this element type
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name of this element
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the context of this element
     *
     * @param string $context
     */
    public function set_context($context)
    {
        $this->set_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the method of this element
     *
     * @param string $method
     */
    public function set_method($method)
    {
        $this->set_property(self::PROPERTY_METHOD, $method);
    }

    /**
     * Sets the parameters of this element
     *
     * @param string[] $parameters
     */
    public function set_parameters($parameters)
    {
        $this->set_property(self::PROPERTY_PARAMETERS, $parameters);
    }

    /**
     * Returns this element as an array
     *
     * @return string[]
     */
    public function as_array()
    {
        return $this->properties;
    }
}
