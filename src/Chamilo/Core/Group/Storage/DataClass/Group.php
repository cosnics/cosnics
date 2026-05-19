<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Sven Vanpoucke
 */
class Group extends DataClass implements UuidDataClassInterface
{
    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_CODE = 'code';
    public const string PROPERTY_DESCRIPTION = 'description';
    public const string PROPERTY_LEFT_VALUE = 'left_value';
    public const string PROPERTY_NAME = 'name';
    public const string PROPERTY_PARENT_ID = 'parent_id';
    public const string PROPERTY_RIGHT_VALUE = 'right_value';

    public static function getAlias(): string
    {
        return 't_grp_grp';
    }

    public function getCode(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_CODE);
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_DESCRIPTION;
        $extendedPropertyNames[] = self::PROPERTY_CODE;
        $extendedPropertyNames[] = self::PROPERTY_PARENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_LEFT_VALUE;
        $extendedPropertyNames[] = self::PROPERTY_RIGHT_VALUE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getDescription(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    public function getId(): ?string
    {
        $identifier = $this->getDefaultProperty(self::PROPERTY_ID);

        if ($identifier) {
            $identifier = Uuid::fromString($identifier)->toString();
        }

        return $identifier;
    }

    public function getLeftValue(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_LEFT_VALUE);
    }

    public function getName(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    public function getParentId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT_ID);
    }

    public function getRightValue(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_RIGHT_VALUE);
    }

    public static function getStorageUnitName(): string
    {
        return 'group_group';
    }

    public function hasChildren(): bool
    {
        return !($this->getLeftValue() == ($this->getRightValue() - 1));
    }

    public function isRoot(): bool
    {
        return ($this->getParentId() == 0);
    }

    public function setCode(?string $code): static
    {
        $this->setDefaultProperty(self::PROPERTY_CODE, $code);

        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);

        return $this;
    }

    public function setLeftValue(int $leftValue): static
    {
        $this->setDefaultProperty(self::PROPERTY_LEFT_VALUE, $leftValue);

        return $this;
    }

    public function setName(?string $name): static
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);

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
