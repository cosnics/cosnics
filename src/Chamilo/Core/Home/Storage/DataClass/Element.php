<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Manager;
use Chamilo\Libraries\Storage\DataClass\ConfigurableDataClassInterface;
use Chamilo\Libraries\Storage\DataClass\ConfigurableDataClassTrait;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;

/**
 * @package Chamilo\Core\Home\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Element extends DataClass implements DataClassDisplayOrderSupport, ConfigurableDataClassInterface
{
    use ConfigurableDataClassTrait;

    public const CONFIGURATION_BLOCK_TYPE = 'block_type';
    public const CONFIGURATION_CONTEXT = 'context';
    public const CONFIGURATION_VISIBILITY = 'visibility';
    public const CONFIGURATION_WIDTH = 'width';
    
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_PARENT_ID = 'parent_id';
    public const PROPERTY_SORT = 'sort';
    public const PROPERTY_TITLE = 'title';
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_USER_ID = 'user_id';

    public const TYPE_BLOCK = 'Chamilo\Core\Home\Storage\DataClass\Block';
    public const TYPE_COLUMN = 'Chamilo\Core\Home\Storage\DataClass\Column';
    public const TYPE_TAB = 'Chamilo\Core\Home\Storage\DataClass\Tab';

    public function getBlockType(): ?string
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            return (string) $this->getSetting(self::CONFIGURATION_BLOCK_TYPE);
        }

        return null;
    }

    public function getContext(): ?string
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            return (string) $this->getSetting(self::CONFIGURATION_CONTEXT);
        }

        return null;
    }

    public static function getDefaultPropertyNamesForConfigurableClass(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_PARENT_ID;
        $extendedPropertyNames[] = self::PROPERTY_TITLE;
        $extendedPropertyNames[] = self::PROPERTY_SORT;
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames(): array
    {
        return [self::PROPERTY_PARENT_ID];
    }

    public function getDisplayOrderPropertyName(): string
    {
        return self::PROPERTY_SORT;
    }

    public function getParentId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT_ID);
    }

    public function getSort(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    public static function getStorageUnitName(): string
    {
        return 'home_element';
    }

    public function getTitle(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_TITLE);
    }

    public function getType(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function getUserId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function getVisibility(): ?bool
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            return (bool) $this->getSetting(self::CONFIGURATION_VISIBILITY);
        }

        return null;
    }

    public function getWidth(): ?int
    {
        if ($this->getType() == self::TYPE_COLUMN)
        {
            return (int) $this->getSetting(self::CONFIGURATION_WIDTH);
        }

        return null;
    }

    public function isBlock(): bool
    {
        return $this->getType() === self::TYPE_BLOCK;
    }

    public function isColumn(): bool
    {
        return $this->getType() === self::TYPE_COLUMN;
    }

    public function isOnTopLevel(): bool
    {
        return $this->getParentId() == 0;
    }

    public function isTab(): bool
    {
        return $this->getType() === self::TYPE_TAB;
    }

    public function isVisible(): ?bool
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            return $this->getVisibility();
        }

        return null;
    }

    public function removeSetting(string $variable): Element
    {
        $configuration = $this->getConfiguration();
        unset($configuration[$variable]);

        $this->setConfiguration($configuration);

        return $this;
    }

    public function setBlockType(string $blockType): Element
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            $this->setSetting(self::CONFIGURATION_BLOCK_TYPE, $blockType);
        }

        return $this;
    }

    public function setContext(string $context): Element
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            $this->setSetting(self::CONFIGURATION_CONTEXT, $context);
        }

        return $this;
    }

    public function setParentId(string $parentId): Element
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT_ID, $parentId);

        return $this;
    }

    public function setSort(int $sort): Element
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);

        return $this;
    }

    public function setTitle(string $title): Element
    {
        $this->setDefaultProperty(self::PROPERTY_TITLE, $title);

        return $this;
    }

    public function setType(string $type): Element
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);

        return $this;
    }

    public function setUserId(string $userId): Element
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);

        return $this;
    }

    public function setVisibility(bool $visibility): Element
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            $this->setSetting(self::CONFIGURATION_VISIBILITY, $visibility);
        }

        return $this;
    }

    public function setWidth(int $width): Element
    {
        if ($this->getType() == self::TYPE_COLUMN)
        {
            $this->setSetting(self::CONFIGURATION_WIDTH, $width);
        }

        return $this;
    }
}