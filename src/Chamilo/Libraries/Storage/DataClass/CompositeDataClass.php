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
    public const PROPERTIES_ADDITIONAL = 'additional_properties';
    public const PROPERTY_TYPE = 'type';

    /**
     *
     * @param string[] $defaultProperties
     * @param string[] $additionalProperties
     *
     * @throws \Exception
     */
    public function __construct(?array $defaultProperties = [], array $additionalProperties = [])
    {
        parent::__construct($defaultProperties);
        $this->setAdditionalProperties($additionalProperties);
        $this->setType(static::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function checkForAdditionalProperties(): CompositeDataClass
    {
        $additionalProperties = $this->getSpecificProperties(self::PROPERTIES_ADDITIONAL);

        if (empty($additionalProperties))
        {
            /**
             * @var \Chamilo\Libraries\Storage\DataManager\DataManager $dataManagerClassName
             */
            $dataManagerClassName = $this::package() . '\Storage\DataManager';

            $this->setAdditionalProperties(
                $dataManagerClassName::retrieve_composite_data_class_additional_properties($this)
            );
        }

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
        foreach ($object::getAdditionalPropertyNames() as $property)
        {
            if (array_key_exists($property, $record))
            {
                $object->setAdditionalProperty($property, $record[$property]);
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

    /**
     * @return string[] An associative array containing the properties.
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAdditionalProperties(): array
    {
        $this->checkForAdditionalProperties();

        return $this->getSpecificProperties(self::PROPERTIES_ADDITIONAL);
    }

    /**
     * @return ?string
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAdditionalProperty(string $name)
    {
        $this->checkForAdditionalProperties();

        return $this->getSpecificProperty(self::PROPERTIES_ADDITIONAL, $name);
    }

    /**
     * @return string[]
     */
    public static function getAdditionalPropertyNames(): array
    {
        return [];
    }

    public function getType(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    /**
     * @deprecated Use CompositeDataClass::getType() now
     */
    public function get_type(): string
    {
        return $this->getType();
    }

    public static function isAdditionalPropertyName(string $name): bool
    {
        return in_array($name, static::getAdditionalPropertyNames());
    }

    /**
     *
     * @return bool
     */
    public static function isExtended(): bool
    {
        return count(static::getAdditionalPropertyNames()) > 0;
    }

    public static function parentClassName(): string
    {
        return get_parent_class(static::class);
    }

    /**
     * @param string[] $additionalProperties
     */
    public function setAdditionalProperties(array $additionalProperties)
    {
        $this->setSpecificProperties(self::PROPERTIES_ADDITIONAL, $additionalProperties);
    }

    /**
     * @param mixed $value The new value for the property.
     */
    public function setAdditionalProperty(string $name, $value)
    {
        $this->setSpecificProperty(self::PROPERTIES_ADDITIONAL, $name, $value);
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
     * @throws \Exception
     * @deprecated Use CompositeDataClass::setType() now
     */
    public function set_type(string $type): CompositeDataClass
    {
        $this->setType($type);

        return $this;
    }
}
