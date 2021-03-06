<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 *
 * @package Chamilo\Libraries\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CompositeDataClass extends DataClass
{
    const PROPERTY_TYPE = 'type';
    const PROPERTIES_ADDITIONAL = 'additional_properties';

    /**
     *
     * @param string[] $defaultProperties
     * @param string[] $additionalProperties
     */
    public function __construct($defaultProperties = array(), $additionalProperties = null)
    {
        parent::__construct($defaultProperties);
        $this->set_additional_properties($additionalProperties);
        $this->set_type(self::class_name());
    }

    /**
     *
     * @param string $class
     * @param string[] $record
     * @throws \Exception
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass
     */
    public static function factory($class, &$record = [])
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
     * @return string[] the aditional property names
     */
    public static function get_additional_property_names()
    {
        return array();
    }

    /**
     * Gets an additional (type-specific) property of this object by name.
     *
     * @param string $name The name of the property.
     */
    public function get_additional_property($name)
    {
        $this->check_for_additional_properties();
        return $this->get_specific_property(self::PROPERTIES_ADDITIONAL, $name);
    }

    /**
     * Sets an additional (type-specific) property of this object by name.
     *
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    public function set_additional_property($name, $value)
    {
        $this->set_specific_property(self::PROPERTIES_ADDITIONAL, $name, $value);
    }

    /**
     * Gets the additional (type-specific) properties of this object.
     *
     * @return string[] An associative array containing the properties.
     */
    public function get_additional_properties()
    {
        $this->check_for_additional_properties();
        return $this->get_specific_properties(self::PROPERTIES_ADDITIONAL);
    }

    /**
     * Sets the additional (type-specific) properties of this object.
     *
     * @param string[] An associative array containing the properties.
     */
    public function set_additional_properties($additional_properties)
    {
        $this->set_specific_properties(self::PROPERTIES_ADDITIONAL, $additional_properties);
    }

    /**
     *
     * @param string $name
     * @return boolean
     */
    static public function is_additional_property_name($name)
    {
        return in_array($name, static::get_additional_property_names());
    }

    /**
     *
     * @return boolean
     */
    public static function is_extended()
    {
        return count(static::get_additional_property_names()) > 0;
    }

    /**
     *
     * @return string
     */
    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     *
     * @param string $type
     */
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
