<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Core\Home\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Element extends DataClass implements DisplayOrderDataClassListenerSupport
{
    public const CONFIGURATION_BLOCK_TYPE = 'block_type';
    public const CONFIGURATION_CONTEXT = 'context';
    public const CONFIGURATION_VISIBILITY = 'visibility';
    public const CONFIGURATION_WIDTH = 'width';
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CONFIGURATION = 'configuration';
    public const PROPERTY_PARENT_ID = 'parent_id';
    public const PROPERTY_SORT = 'sort';
    public const PROPERTY_TITLE = 'title';
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_USER_ID = 'user_id';

    public const TYPE_BLOCK = 'Chamilo\Core\Home\Storage\DataClass\Block';
    public const TYPE_COLUMN = 'Chamilo\Core\Home\Storage\DataClass\Column';
    public const TYPE_TAB = 'Chamilo\Core\Home\Storage\DataClass\Tab';

    /**
     * @throws \Exception
     */
    public function __construct(array $defaultProperties = [], array $additionalProperties = [])
    {
        parent::__construct($defaultProperties, $additionalProperties);
        $this->addListener(new DisplayOrderDataClassListener($this));
    }

    public function getBlockType(): ?string
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            return $this->getSetting(self::CONFIGURATION_BLOCK_TYPE);
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getConfiguration(): array
    {
        return unserialize($this->getDefaultProperty(self::PROPERTY_CONFIGURATION));
    }

    public function getContext(): ?string
    {
        if ($this->getType() == self::TYPE_BLOCK)
        {
            return $this->getSetting(self::CONFIGURATION_CONTEXT);
        }

        return null;
    }

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_TYPE,
                self::PROPERTY_PARENT_ID,
                self::PROPERTY_TITLE,
                self::PROPERTY_SORT,
                self::PROPERTY_USER_ID,
                self::PROPERTY_CONFIGURATION
            ]
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function getDisplayOrderContextProperties(): array
    {
        return [new PropertyConditionVariable(Element::class, self::PROPERTY_PARENT_ID)];
    }

    public function getDisplayOrderProperty(): PropertyConditionVariable
    {
        return new PropertyConditionVariable(Element::class, self::PROPERTY_SORT);
    }

    public function getParentId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT_ID);
    }

    public function getSetting(string $variable, $defaultValue = null): string
    {
        $configuration = $this->getConfiguration();

        return ($configuration[$variable] ?? $defaultValue);
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

    /**
     * @param string[] $configuration
     */
    public function setConfiguration(array $configuration): Element
    {
        $this->setDefaultProperty(self::PROPERTY_CONFIGURATION, serialize($configuration));

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

    public function setSetting(string $variable, string $value): Element
    {
        $configuration = $this->getConfiguration();
        $configuration[$variable] = $value;

        $this->setConfiguration($configuration);

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
            $this->setSetting(self::CONFIGURATION_VISIBILITY, (string) (int) $visibility);
        }

        return $this;
    }

    public function setWidth(int $width): Element
    {
        if ($this->getType() == self::TYPE_COLUMN)
        {
            $this->setSetting(self::CONFIGURATION_WIDTH, (string) $width);
        }

        return $this;
    }
}