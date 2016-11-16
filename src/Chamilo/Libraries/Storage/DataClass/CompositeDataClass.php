<?php
namespace Chamilo\Libraries\Storage\DataClass;

abstract class CompositeDataClass extends DataClass
{
    const PROPERTY_TYPE = 'type';
    const PROPERTIES_ADDITIONAL = 'additional_properties';

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent::__construct($default_properties);
        $this->set_additional_properties($additional_properties);
        $this->set_type(self::class_name());
    }

    /**
     *
     * @param $class string
     * @param $record multitype:string
     * @throws Exception
     * @return \libraries\storage\CompositeDataClass
     */
    public static function factory($class, &$record)
    {
        $object = parent::factory($class, $record);
        foreach ($object->get_additional_property_names() as $property)
        {
            if (array_key_exists($property, $record))
            {
                $object->set_additional_property($property, $record[$property]);
                unset($record[$property]);
            }
        }
        if (count($record) > 0 && $object instanceof CompositeDataClass)
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $object->set_optional_property($optional_property_name, $optional_property_value);
            }
        }
        return $object;
    }

    /**
     * Gets the additional property names Should be overridden when needed
     * 
     * @return array the aditional property names
     */
    public static function get_additional_property_names()
    {
        return array();
    }

    /**
     * Gets an additional (type-specific) property of this object by name.
     * 
     * @param $name string The name of the property.
     */
    public function get_additional_property($name)
    {
        $this->check_for_additional_properties();
        return $this->get_specific_property(self::PROPERTIES_ADDITIONAL, $name);
    }

    /**
     * Sets an additional (type-specific) property of this object by name.
     * 
     * @param $name string The name of the property.
     * @param $value mixed The new value for the property.
     */
    public function set_additional_property($name, $value)
    {
        $this->set_specific_property(self::PROPERTIES_ADDITIONAL, $name, $value);
    }

    /**
     * Gets the additional (type-specific) properties of this object.
     * 
     * @return array An associative array containing the properties.
     */
    public function get_additional_properties()
    {
        $this->check_for_additional_properties();
        return $this->get_specific_properties(self::PROPERTIES_ADDITIONAL);
    }

    /**
     * Sets the additional (type-specific) properties of this object.
     * 
     * @param array An associative array containing the properties.
     */
    public function set_additional_properties($additional_properties)
    {
        $this->set_specific_properties(self::PROPERTIES_ADDITIONAL, $additional_properties);
    }

    static public function is_additional_property_name($name)
    {
        return in_array($name, static::get_additional_property_names());
    }

    public static function is_extended()
    {
        return count(static::get_additional_property_names()) > 0;
    }

    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    public function check_for_additional_properties()
    {
        $additional_properties = $this->get_specific_properties(self::PROPERTIES_ADDITIONAL);
        if (isset($additional_properties) && ! empty($additional_properties))
        {
            return;
        }
        
        $data_manager = $this->package() . '\Storage\DataManager';
        
        $this->set_additional_properties($data_manager::retrieve_composite_data_class_additional_properties($this));
    }

    /**
     * Get the fully qualified class name of the DataClass object
     * 
     * @return string
     */
    public static function parent_class_name()
    {
        return get_parent_class(static::class_name());
    }
}
