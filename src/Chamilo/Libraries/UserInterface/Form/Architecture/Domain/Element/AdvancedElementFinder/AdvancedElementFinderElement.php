<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder;

/**
 * Defines an element for an advanced element finder
 * When the element has children it becomes a category
 *
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder
 * @author Sven Vanpoucke
 */
class AdvancedElementFinderElement
{
    public const string PROPERTY_CHILDREN = 'children';
    public const string PROPERTY_CLASS = 'classes';
    public const string PROPERTY_DESCRIPTION = 'description';
    public const string PROPERTY_ID = 'id';
    public const string PROPERTY_TITLE = 'title';
    public const string PROPERTY_TYPE = 'type';
    public const int TYPE_FILTER = 3;
    public const int TYPE_SELECTABLE = 1;
    public const int TYPE_SELECTABLE_AND_FILTER = 2;
    public const int TYPE_VISUAL = 4;

    private array $properties;

    public function __construct(
        string $id, string $class, string $title, string $description, int $type = self::TYPE_SELECTABLE
    )
    {
        $this->setId($id);
        $this->setClass($class);
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setType($type);
        $this->setChildren([]);
    }

    public function addChild(AdvancedElementFinderElement $child): static
    {
        $children = $this->getChildren();

        $children[] = $child;

        $this->setChildren($children);

        return $this;
    }

    /**
     * @return string[]
     */
    public function asArray(): array
    {
        $array = $this->properties;
        $array[self::PROPERTY_CHILDREN] = [];

        $children = $this->getChildren();

        foreach ($children as $child) {
            $array[self::PROPERTY_CHILDREN][] = $child->asArray();
        }

        return $array;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement[]
     */
    public function getChildren(): array
    {
        return $this->getProperty(self::PROPERTY_CHILDREN);
    }

    public function getId(): string
    {
        return $this->getProperty(self::PROPERTY_ID);
    }

    public function getProperty(string $propertyName): mixed
    {
        return $this->properties[$propertyName];
    }

    public function hasChildren(): bool
    {
        return count($this->getChildren()) > 0;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElement[] $children
     */
    public function setChildren(array $children = []): static
    {
        $this->setProperty(self::PROPERTY_CHILDREN, $children);

        return $this;
    }

    public function setClass(string $class): static
    {
        $this->setProperty(self::PROPERTY_CLASS, $class);

        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->setProperty(self::PROPERTY_DESCRIPTION, $description);

        return $this;
    }

    public function setId(string $id): static
    {
        $this->setProperty(self::PROPERTY_ID, $id);

        return $this;
    }

    public function setProperty(string $propertyName, mixed $value): static
    {
        $this->properties[$propertyName] = $value;

        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->setProperty(self::PROPERTY_TITLE, $title);

        return $this;
    }

    public function setType(int $type): static
    {
        $this->setProperty(self::PROPERTY_TYPE, $type);

        return $this;
    }
}
