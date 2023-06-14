<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ConfigurableDataClassTrait
{
    public const PROPERTY_CONFIGURATION = 'configuration';

    /**
     * @return string[]
     */
    public function getConfiguration(): array
    {
        $serializedConfiguration = $this->getDefaultProperty(self::PROPERTY_CONFIGURATION);

        return unserialize($serializedConfiguration ?: serialize([]));
    }

    /**
     * @return mixed
     */
    abstract public function getDefaultProperty(string $name);

    public function getSetting(string $variable, mixed $defaultValue = null): mixed
    {
        $configuration = $this->getConfiguration();

        return ($configuration[$variable] ?? $defaultValue);
    }

    /**
     * @param string[] $configuration
     */
    public function setConfiguration(array $configuration): static
    {
        $this->setDefaultProperty(self::PROPERTY_CONFIGURATION, serialize($configuration));

        return $this;
    }

    /**
     * @param mixed $value
     */
    abstract public function setDefaultProperty(string $name, $value);

    public function setSetting(string $variable, mixed $value): static
    {
        $configuration = $this->getConfiguration();
        $configuration[$variable] = $value;

        $this->setConfiguration($configuration);

        return $this;
    }
}