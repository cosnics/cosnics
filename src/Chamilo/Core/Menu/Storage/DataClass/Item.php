<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Storage\DataClass\ConfigurableDataClassInterface;
use Chamilo\Libraries\Storage\DataClass\ConfigurableDataClassTrait;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;

/**
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Item extends DataClass implements DataClassDisplayOrderSupport, ConfigurableDataClassInterface
{
    use ConfigurableDataClassTrait;

    public const CONTEXT = Manager::CONTEXT;

    public const DISPLAY_BOTH = 3;
    public const DISPLAY_ICON = 1;
    public const DISPLAY_TEXT = 2;

    public const PROPERTY_DISPLAY = 'display';
    public const PROPERTY_HIDDEN = 'hidden';
    public const PROPERTY_ICON_CLASS = 'icon_class';
    public const PROPERTY_PARENT = 'parent';
    public const PROPERTY_SORT = 'sort';

    public const PROPERTY_TITLES = 'titles';

    public const PROPERTY_TYPE = 'type';

    public const TYPE_APPLICATION = 1;
    public const TYPE_CATEGORY = 3;
    public const TYPE_LINK = 2;
    public const TYPE_LINK_APPLICATION = 4;

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

    public function getDisplay(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY);
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

    /**
     * @string mixed
     */
    public function getIconClass()
    {
        return $this->getDefaultProperty(self::PROPERTY_ICON_CLASS);
    }

    public function getParentId(): int
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

    /**
     * @deprecated Use Item::getDisplay() now
     */
    public function get_display(): int
    {
        return $this->getDisplay();
    }

    /**
     * @deprecated Use Item::getHidden() now
     */
    public function get_hidden(): int
    {
        return $this->getHidden();
    }

    /**
     * @deprecated Use Item::getParent() now
     */
    public function get_parent(): int
    {
        return $this->getParentId();
    }

    /**
     * @deprecated Use Item::getSort() now
     */
    public function get_sort(): int
    {
        return $this->getSort();
    }

    /**
     * @deprecated Use Item::hadParentId() now
     */
    public function hasParent(): bool
    {
        return $this->hasParentId();
    }

    public function hasParentId(): bool
    {
        return $this->getParentId() != 0;
    }

    public function isHidden(): bool
    {
        return (bool) $this->getHidden();
    }

    /**
     * @deprecated Use Item::isHidden() now
     */
    public function is_hidden(): bool
    {
        return $this->isHidden();
    }

    public function setDisplay(int $display = self::DISPLAY_ICON): Item
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY, $display);

        return $this;
    }

    public function setHidden(int $hidden = 0): Item
    {
        $this->setDefaultProperty(self::PROPERTY_HIDDEN, $hidden);

        return $this;
    }

    public function setIconClass(string $iconClass = ''): Item
    {
        $this->setDefaultProperty(self::PROPERTY_ICON_CLASS, $iconClass);

        return $this;
    }

    public function setParentId(int $parent): Item
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

    /**
     * @deprecated Use Item::setDisplay() now
     */
    public function set_display(int $display = self::DISPLAY_ICON): Item
    {
        return $this->setDisplay($display);
    }

    /**
     * @deprecated User Item::setHidden() now
     */
    public function set_hidden(int $hidden = 0): Item
    {
        return $this->setHidden($hidden);
    }

    /**
     * @deprecated Use Item::setParent() now
     */
    public function set_parent(int $parent): Item
    {
        return $this->setParentId($parent);
    }

    /**
     * @deprecated Use Item::setSort() now
     */
    public function set_sort(int $sort): Item
    {
        $this->setSort($sort);

        return $this;
    }

    public function showIcon(): bool
    {
        return $this->getDisplay() == self::DISPLAY_BOTH || $this->getDisplay() == self::DISPLAY_ICON;
    }

    public function showTitle(): bool
    {
        return $this->getDisplay() == self::DISPLAY_TEXT || $this->getDisplay() == self::DISPLAY_BOTH;
    }

    /**
     * @deprecated Use Item::showIcon() now
     */
    public function show_icon(): bool
    {
        return $this->showIcon();
    }

    /**
     * @deprecated Use Item::showTitle() now
     */
    public function show_title(): bool
    {
        return $this->showTitle();
    }

}
