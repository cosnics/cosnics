<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Interface\ConfigurableDataClassInterface;
use Chamilo\Libraries\Storage\Architecture\Interface\DataClassDisplayOrderSupport;
use Chamilo\Libraries\Storage\Architecture\Interface\UuidDataClassInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\ConfigurableDataClassTrait;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Item extends DataClass
    implements DataClassDisplayOrderSupport, ConfigurableDataClassInterface, UuidDataClassInterface
{
    use ConfigurableDataClassTrait;

    public const string CONTEXT = Manager::CONTEXT;
    public const string PROPERTY_DISPLAY = 'display';
    public const string PROPERTY_HIDDEN = 'hidden';
    public const string PROPERTY_ICON_CLASS = 'icon_class';
    public const string PROPERTY_PARENT = 'parent_id';
    public const string PROPERTY_SORT = 'sort';
    public const string PROPERTY_TITLES = 'titles';
    public const string PROPERTY_TYPE = 'type';

    public function getDefaultPropertiesUnserialized(): array
    {
        $defaultProperties = $this->getDefaultProperties();
        
        $defaultProperties[self::PROPERTY_TITLES] = $this->getTitles();
        $defaultProperties[self::PROPERTY_CONFIGURATION] = $this->getConfiguration();

        return $defaultProperties;
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_TYPE,
                self::PROPERTY_PARENT,
                self::PROPERTY_TYPE,
                self::PROPERTY_SORT,
                self::PROPERTY_HIDDEN,
                self::PROPERTY_DISPLAY,
                self::PROPERTY_ICON_CLASS,
                self::PROPERTY_CONFIGURATION,
                self::PROPERTY_TITLES
            ]
        );
    }

    public function getDisplay(): DisplayTypeEnum
    {
        return DisplayTypeEnum::from($this->getDefaultProperty(self::PROPERTY_DISPLAY));
    }

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames(): array
    {
        return [self::PROPERTY_PARENT];
    }

    public function getDisplayOrderPropertyName(): string
    {
        return self::PROPERTY_SORT;
    }

    public function getGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('file', [], null, 'fas');
    }

    public function getHidden(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_HIDDEN);
    }

    public function getIconClass(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_ICON_CLASS);
    }

    public function getParentId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT);
    }

    public function getSort(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    public static function getStorageUnitName(): string
    {
        return 'menu_item';
    }

    public static function getAlias(): string
    {
        return 't_mnu_itm';
    }

    public function getTitleForIsoCode(string $isoCode): ?string
    {
        return $this->getSerializedPropertyValue(self::PROPERTY_TITLES, $isoCode);
    }

    /**
     * @return string[]
     */
    public function getTitles(): array
    {
        return $this->getSerializedProperty(self::PROPERTY_TITLES);
    }

    public function getType(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function hasParentId(): bool
    {
        return $this->getParentId() != 0;
    }

    public function isHidden(): bool
    {
        return (bool) $this->getHidden();
    }

    public function setDisplay(DisplayTypeEnum $display = DisplayTypeEnum::ICON): Item
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY, $display->value);

        return $this;
    }

    public function setHidden(int $hidden = 0): Item
    {
        $this->setDefaultProperty(self::PROPERTY_HIDDEN, $hidden);

        return $this;
    }

    public function setIconClass(?string $iconClass = null): Item
    {
        $this->setDefaultProperty(self::PROPERTY_ICON_CLASS, $iconClass);

        return $this;
    }

    public function setParentId(string $parent): Item
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT, $parent);

        return $this;
    }

    public function setSort(?int $sort): Item
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);

        return $this;
    }

    public function setTitleForIsoCode(string $isoCode, string $title): Item
    {
        return $this->setSerializedPropertyValue(self::PROPERTY_TITLES, $isoCode, $title);
    }

    /**
     * @param string[] $titles
     */
    public function setTitles(array $titles): Item
    {
        return $this->setSerializedProperty(self::PROPERTY_TITLES, $titles);
    }

    public function setType(string $type): Item
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);

        return $this;
    }

    public function showIcon(): bool
    {
        return $this->getDisplay() == DisplayTypeEnum::ICON_AND_LABEL || $this->getDisplay() == DisplayTypeEnum::ICON;
    }

    public function showTitle(): bool
    {
        return $this->getDisplay() == DisplayTypeEnum::ICON_AND_LABEL || $this->getDisplay() == DisplayTypeEnum::LABEL;
    }
}
