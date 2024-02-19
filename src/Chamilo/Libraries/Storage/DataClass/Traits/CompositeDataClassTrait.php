<?php
namespace Chamilo\Libraries\Storage\DataClass\Traits;

use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
trait CompositeDataClassTrait
{
    public const PROPERTIES_ADDITIONAL = 'additional_properties';
    public const PROPERTY_TYPE = 'type';

    public function checkForAdditionalProperties(): static
    {
        $additionalProperties = $this->getSpecificProperties(self::PROPERTIES_ADDITIONAL);

        if (empty($additionalProperties) && $this->isIdentified() && static::hasAdditionalPropertyNames())
        {
            $this->setAdditionalProperties(
                DataManager::retrieve_composite_data_class_additional_properties(static::class, $this->getId())
            );
        }

        return $this;
    }

    /**
     * @return string[] An associative array containing the properties.
     */
    public function getAdditionalProperties(): array
    {
        $this->checkForAdditionalProperties();

        return $this->getSpecificProperties(self::PROPERTIES_ADDITIONAL);
    }

    public function getAdditionalProperty(string $name): mixed
    {
        $this->checkForAdditionalProperties();

        return $this->getSpecificProperty(self::PROPERTIES_ADDITIONAL, $name);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getAdditionalPropertyNames(array $extendedPropertyNames = []): array
    {
        return $extendedPropertyNames;
    }

    abstract public function getDefaultProperty(string $name): mixed;

    abstract public function getId(): string;

    abstract public function getSpecificProperties(string $propertiesType): array;

    abstract public function getSpecificProperty(string $propertiesType, string $propertyName): mixed;

    public function getType(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public static function hasAdditionalPropertyNames(): bool
    {
        return count(static::getAdditionalPropertyNames()) > 0;
    }

    public static function isAdditionalPropertyName(string $name): bool
    {
        return in_array($name, static::getAdditionalPropertyNames());
    }

    abstract public function isIdentified(): bool;

    /**
     * @param string[] $additionalProperties
     */
    public function setAdditionalProperties(array $additionalProperties): static
    {
        $this->setSpecificProperties(self::PROPERTIES_ADDITIONAL, $additionalProperties);

        return $this;
    }

    public function setAdditionalProperty(string $name, mixed $value): static
    {
        $this->setSpecificProperty(self::PROPERTIES_ADDITIONAL, $name, $value);

        return $this;
    }

    abstract public function setDefaultProperty(string $name, mixed $value): static;

    abstract public function setSpecificProperties(string $propertiesType, array $properties): static;

    abstract public function setSpecificProperty(string $propertiesType, string $propertyName, mixed $propertyValue
    ): static;

    public function setType(string $type): static
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);

        return $this;
    }
}
