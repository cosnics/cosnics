<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder;

/**
 * class that describes a type for the advanced element finder
 *
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder
 */
class AdvancedElementFinderElementType
{
    public const PROPERTY_CONTEXT = 'application';
    public const PROPERTY_ID = 'id';
    public const PROPERTY_METHOD = 'go';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_PARAMETERS = 'parameters';

    private array $properties;

    /**
     * @param string[] $parameters
     */
    public function __construct(string $id, string $name, string $context, string $method, array $parameters = [])
    {
        $this->setId($id);
        $this->setName($name);
        $this->setContext($context);
        $this->setMethod($method);
        $this->setParameters($parameters);
    }

    /**
     * @return string[]
     */
    public function asArray(): array
    {
        return $this->properties;
    }

    public function getId(): string
    {
        return $this->getProperty(self::PROPERTY_ID);
    }

    public function getName(): string
    {
        return $this->getProperty(self::PROPERTY_NAME);
    }

    public function getProperty(string $propertyName): mixed
    {
        return $this->properties[$propertyName];
    }

    public function setContext(string $context): static
    {
        $this->setProperty(self::PROPERTY_CONTEXT, $context);

        return $this;
    }

    public function setId(string $id): static
    {
        $this->setProperty(self::PROPERTY_ID, $id);

        return $this;
    }

    public function setMethod(string $method): static
    {
        $this->setProperty(self::PROPERTY_METHOD, $method);

        return $this;
    }

    public function setName(string $name): static
    {
        $this->setProperty(self::PROPERTY_NAME, $name);

        return $this;
    }

    public function setParameters(array $parameters): static
    {
        $this->setProperty(self::PROPERTY_PARAMETERS, $parameters);

        return $this;
    }

    public function setProperty(string $propertyName, mixed $value): static
    {
        $this->properties[$propertyName] = $value;

        return $this;
    }
}
