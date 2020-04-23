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
    const PROPERTIES_ADDITIONAL = 'additional_properties';

    const PROPERTY_TYPE = 'type';

    /**
     *
     * @param string[] $defaultProperties
     * @param string[] $additionalProperties
     *
     * @throws \Exception
     */
    public function __construct($defaultProperties = array(), $additionalProperties = null)
    {
        parent::__construct($defaultProperties);
        $this->set_additional_properties($additionalProperties);
        $this->setType(static::class);
    }

    public function check_for_additional_properties()
    {
        $additional_properties = $this->get_specific_properties(self::PROPERTIES_ADDITIONAL);

        if (isset($additional_properties) && !empty($additional_properties))
        {
            return;
        }

        /**
         * @var \Chamilo\Libraries\Storage\DataManager\DataManager $data_manager
         */
        $data_manager = $this->package() . '\Storage\DataManager';

        $this->set_additional_properties($data_manager::retrieve_composite_data_class_additional_properties($this));
    }

    /**
     *
     * @param string $class
     * @param string[] $record
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass
     * @throws \Exception
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
     *
     * @return string
     */
    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
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
     * Gets an additional (type-specific) property of this object by name.
     *
     * @param string $name The name of the property.
     *
     * @return string
     */
    public function get_additional_property($name)
    {
        $this->check_for_additional_properties();

        return $this->get_specific_property(self::PROPERTIES_ADDITIONAL, $name);
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
     *
     * @return string
     * @deprecated User CompositeDataClass::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     *
     * @param string $name
     *
     * @return boolean
     */
    static public function isAdditionalPropertyName($name)
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
     * Get the fully qualified class name of the DataClass object
     *
     * @return string
     */
    public static function parent_class_name()
    {
        return get_parent_class(static::class);
    }

    /**
     * @param string $type
     *
     * @throws \Exception
     */
    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
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
     *
     * @param string $type
     *
     * @throws \Exception
     * @deprecated Use CompositeDataClass::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }
}
