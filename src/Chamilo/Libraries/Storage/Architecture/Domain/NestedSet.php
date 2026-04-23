<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

/**
 * This class extends Dataclass to provide auxiliary methods which allows using its subclasses as tree-structured data.
 * It is aimed to replace nested_tree_node and all ad hoc implementations.
 *
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 */
abstract class NestedSet extends DataClass
{
    public const int AS_FIRST_CHILD_OF = 1;
    public const int AS_LAST_CHILD_OF = 2;
    public const int AS_NEXT_SIBLING_OF = 4;
    public const int AS_PREVIOUS_SIBLING_OF = 3;
    public const string PROPERTY_LEFT_VALUE = 'left_value';
    public const string PROPERTY_PARENT_ID = 'parent_id';
    public const string PROPERTY_RIGHT_VALUE = 'right_value';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_PARENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_LEFT_VALUE;
        $extendedPropertyNames[] = self::PROPERTY_RIGHT_VALUE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getLeftValue(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_LEFT_VALUE);
    }

    public function getParentId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT_ID);
    }

    public function getRightValue(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHT_VALUE);
    }

    /**
     * @return string[]
     */
    public function getSubTreePropertyNames(): array
    {
        return [];
    }

    public function hasChildren(): bool
    {
        return !($this->getLeftValue() == ($this->getRightValue() - 1));
    }

    public function isRoot(): bool
    {
        return ($this->getParentId() == 0);
    }

    public function setLeftValue(int $leftValue): static
    {
        $this->setDefaultProperty(self::PROPERTY_LEFT_VALUE, $leftValue);

        return $this;
    }

    public function setParentId(string $parentId): static
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT_ID, $parentId);

        return $this;
    }

    public function setRightValue(int $rightValue): static
    {
        $this->setDefaultProperty(self::PROPERTY_RIGHT_VALUE, $rightValue);

        return $this;
    }
}
