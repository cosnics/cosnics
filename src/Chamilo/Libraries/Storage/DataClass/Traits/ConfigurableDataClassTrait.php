<?php
namespace Chamilo\Libraries\Storage\DataClass\Traits;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ConfigurableDataClassTrait
{
    use DataClassSerializedPropertyTrait;

    public const PROPERTY_CONFIGURATION = 'configuration';

    public function getConfiguration(): array
    {
        return $this->getSerializedProperty(self::PROPERTY_CONFIGURATION);
    }

    public function getSetting(string $variable, mixed $defaultValue = null): mixed
    {
        return $this->getSerializedPropertyValue(self::PROPERTY_CONFIGURATION, $variable, $defaultValue);
    }

    public function setConfiguration(array $configuration): static
    {
        return $this->setSerializedProperty(self::PROPERTY_CONFIGURATION, $configuration);
    }

    public function setSetting(string $variable, mixed $value): static
    {
        return $this->setSerializedPropertyValue(self::PROPERTY_CONFIGURATION, $variable, $value);
    }
}