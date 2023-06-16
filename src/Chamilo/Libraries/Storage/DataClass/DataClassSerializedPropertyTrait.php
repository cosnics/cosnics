<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataClassSerializedPropertyTrait
{

    abstract public function getDefaultProperty(string $name): mixed;

    /**
     * @return string[]
     */
    public function getSerializedProperty(string $propertyName): array
    {
        $serializedConfiguration = $this->getDefaultProperty($propertyName);

        return $serializedConfiguration ? unserialize($serializedConfiguration) : [];
    }

    public function getSerializedPropertyValue(string $propertyName, string $variable, mixed $defaultValue = null
    ): mixed
    {
        $propertyValues = $this->getSerializedProperty($propertyName);

        return ($propertyValues[$variable] ?? $defaultValue);
    }

    abstract public function setDefaultProperty(string $name, mixed $value);

    /**
     * @param string $propertyName
     * @param array $propertyValues
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClassSerializedPropertyTrait
     */
    public function setSerializedProperty(string $propertyName, array $propertyValues): static
    {
        $this->setDefaultProperty($propertyName, serialize($propertyValues));

        return $this;
    }

    public function setSerializedPropertyValue(string $propertyName, string $variable, mixed $value): static
    {
        $propertyValues = $this->getSerializedProperty($propertyName);
        $propertyValues[$variable] = $value;

        $this->setSerializedProperty($propertyName, $propertyValues);

        return $this;
    }
}