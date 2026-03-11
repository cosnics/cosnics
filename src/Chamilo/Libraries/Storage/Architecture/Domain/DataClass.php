<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 * @author  Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class DataClass
{
    use DependencyInjectionContainerTrait;

    public const string EMPTY_UUID = '00000000-0000-0000-0000-000000000000';
    public const string PROPERTIES_DEFAULT = 'default_properties';
    public const string PROPERTIES_OPTIONAL = 'optional_properties';
    public const string PROPERTY_ID = 'id';

    /**
     * @var string[][]
     */
    private array $properties;

    public function __construct(array $defaultProperties = [], array $optionalProperties = [])
    {
        $this->setDefaultProperties($defaultProperties);
        $this->setOptionalProperties($optionalProperties);
    }

    /**
     * @return string[]
     */
    public function getDefaultProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_DEFAULT);
    }

    public function getDefaultProperty(string $name): mixed
    {
        return $this->getSpecificProperty(self::PROPERTIES_DEFAULT, $name);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = static::PROPERTY_ID;

        return $extendedPropertyNames;
    }

    public function getId(): ?string
    {
        return $this->getDefaultProperty(static::PROPERTY_ID);
    }

    public function getOptionalProperties(): array
    {
        return $this->getSpecificProperties(self::PROPERTIES_OPTIONAL);
    }

    /**
     * @return ?string
     */
    public function getOptionalProperty(string $name): mixed
    {
        return $this->getSpecificProperty(self::PROPERTIES_OPTIONAL, $name);
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param string[][] $properties
     */
    public function setProperties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    public function getSpecificProperties(string $propertiesType): array
    {
        return array_key_exists($propertiesType, $this->properties) ? $this->properties[$propertiesType] : [];
    }

    public function getSpecificProperty(string $propertiesType, string $propertyName): mixed
    {
        $properties = $this->getSpecificProperties($propertiesType);

        return (array_key_exists($propertyName, $properties)) ? $properties[$propertyName] : null;
    }

    abstract public static function getStorageUnitName(): string;

    public static function isDefaultPropertyName(string $name): bool
    {
        return in_array($name, static::getDefaultPropertyNames());
    }

    public function isIdentified(): bool
    {
        return !empty($this->getId());
    }

    public function setDefaultProperties(array $defaultProperties): static
    {
        $this->setSpecificProperties(self::PROPERTIES_DEFAULT, $defaultProperties);

        return $this;
    }

    public function setDefaultProperty(string $name, mixed $value): static
    {
        $this->setSpecificProperty(self::PROPERTIES_DEFAULT, $name, $value);

        return $this;
    }

    public function setId(?string $id): static
    {
        $this->setDefaultProperty(static::PROPERTY_ID, $id);

        return $this;
    }

    /**
     * @param string[] $optionalProperties
     */
    public function setOptionalProperties(array $optionalProperties): static
    {
        $this->setSpecificProperties(self::PROPERTIES_OPTIONAL, $optionalProperties);

        return $this;
    }

    public function setOptionalProperty(string $name, mixed $value): static
    {
        $this->setSpecificProperty(self::PROPERTIES_OPTIONAL, $name, $value);

        return $this;
    }

    /**
     * @param string[] $properties
     */
    public function setSpecificProperties(string $propertiesType, array $properties): static
    {
        $this->properties[$propertiesType] = $properties;

        return $this;
    }

    public function setSpecificProperty(string $propertiesType, string $propertyName, mixed $propertyValue): static
    {
        $this->properties[$propertiesType][$propertyName] = $propertyValue;

        return $this;
    }
}
