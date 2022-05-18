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
    public function __construct(?array $defaultProperties = [], ?array $additionalProperties = null)
    {
        parent::__construct($defaultProperties);
        $this->set_additional_properties($additionalProperties);
        $this->setType(static::class);
    }

    /**
     * @throws \ReflectionException
     */
    public function checkForAdditionalProperties(): CompositeDataClass
    {
        $additionalProperties = $this->getSpecificProperties(self::PROPERTIES_ADDITIONAL);

        if (!empty($additionalProperties))
        {
            return;
        }

        /**
         * @var \Chamilo\Libraries\Storage\DataManager\DataManager $dataManager
         */
        $dataManager = $this->package() . '\Storage\DataManager';

        $this->set_additional_properties($dataManager::retrieve_composite_data_class_additional_properties($this));

        return $this;
    }

    /**
     * @param string[] $record
     *
     * @throws \Exception
     */
    public static function factory(string $class, array &$record = []): CompositeDataClass
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
                $object->setOptionalProperty($optional_property_name, $optional_property_value);
            }
        }

        return $object;
    }

    public function getType(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    /**
     * @return string[] An associative array containing the properties.
     */
    public function get_additional_properties(): array
    {
        $this->checkForAdditionalProperties();

        return $this->getSpecificProperties(self::PROPERTIES_ADDITIONAL);
    }

    /**
     * @return string
     */
    public function get_additional_property(string $name)
    {
        $this->checkForAdditionalProperties();

        return $this->getSpecificProperty(self::PROPERTIES_ADDITIONAL, $name);
    }

    public static function get_additional_property_names(): array
    {
        return [];
    }

    /**
     * @deprecated Use CompositeDataClass::getType() now
     */
    public function get_type(): string
    {
        return $this->getType();
    }

    static public function isAdditionalPropertyName(string $name): bool
    {
        return in_array($name, static::get_additional_property_names());
    }

    /**
     *
     * @return boolean
     */
    public static function isExtended(): bool
    {
        return count(static::get_additional_property_names()) > 0;
    }

    public static function parent_class_name(): string
    {
        return get_parent_class(static::class);
    }

    /**
     * @throws \Exception
     */
    public function setType(string $type): CompositeDataClass
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);

        return $this;
    }

    /**
     * @param string[] $additionalProperties
     */
    public function set_additional_properties(array $additionalProperties)
    {
        $this->setSpecificProperties(self::PROPERTIES_ADDITIONAL, $additionalProperties);
    }

    /**
     * @param mixed $value The new value for the property.
     */
    public function set_additional_property(string $name, $value)
    {
        $this->setSpecificProperty(self::PROPERTIES_ADDITIONAL, $name, $value);
    }

    /**
     * @throws \Exception
     * @deprecated Use CompositeDataClass::setType() now
     */
    public function set_type(string $type): CompositeDataClass
    {
        $this->setType($type);

        return $this;
    }
}
