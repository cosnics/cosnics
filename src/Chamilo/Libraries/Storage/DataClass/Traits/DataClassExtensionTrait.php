<?php
namespace Chamilo\Libraries\Storage\DataClass\Traits;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Storage\Repository\DataManager;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataClassExtensionTrait
{

    public function checkForAdditionalProperties(): static
    {
        $additionalProperties = $this->getSpecificProperties(DataClassExtensionInterface::PROPERTIES_ADDITIONAL);

        if (empty($additionalProperties) && $this->isIdentified())
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

        return $this->getSpecificProperties(DataClassExtensionInterface::PROPERTIES_ADDITIONAL);
    }

    public function getAdditionalProperty(string $name): mixed
    {
        $this->checkForAdditionalProperties();

        return $this->getSpecificProperty(DataClassExtensionInterface::PROPERTIES_ADDITIONAL, $name);
    }

    /**
     * @return string[]
     */
    abstract public static function getAdditionalPropertyNames(): array;

    abstract public function getId(): ?string;

    abstract public function getSpecificProperties(string $propertiesType): array;

    abstract public function getSpecificProperty(string $propertiesType, string $propertyName): mixed;

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
        $this->setSpecificProperties(DataClassExtensionInterface::PROPERTIES_ADDITIONAL, $additionalProperties);

        return $this;
    }

    public function setAdditionalProperty(string $name, mixed $value): static
    {
        $this->setSpecificProperty(DataClassExtensionInterface::PROPERTIES_ADDITIONAL, $name, $value);

        return $this;
    }

    abstract public function setSpecificProperties(string $propertiesType, array $properties): static;

    abstract public function setSpecificProperty(string $propertiesType, string $propertyName, mixed $propertyValue
    ): static;
}
