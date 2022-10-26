<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Item extends CompositeDataClass implements DataClassDisplayOrderSupport
{
    /**
     * Display options
     */
    public const DISPLAY_BOTH = 3;
    public const DISPLAY_ICON = 1;
    public const DISPLAY_TEXT = 2;

    /**
     * Properties
     */
    public const PROPERTY_DISPLAY = 'display';
    public const PROPERTY_HIDDEN = 'hidden';
    public const PROPERTY_ICON_CLASS = 'icon_class';
    public const PROPERTY_PARENT = 'parent';
    public const PROPERTY_SORT = 'sort';

    /**
     * Item types
     */
    public const TYPE_APPLICATION = 1;
    public const TYPE_CATEGORY = 3;
    public const TYPE_LINK = 2;
    public const TYPE_LINK_APPLICATION = 4;

    /**
     * @return int
     */
    public function getDisplay()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY);
    }

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames(): array
    {
        return array(self::PROPERTY_PARENT);
    }

    public function getDisplayOrderPropertyName(): string
    {
        return self::PROPERTY_SORT;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph()
    {
        return new FontAwesomeGlyph('file', [], null, 'fas');
    }

    /**
     * @return int
     */
    public function getHidden()
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

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT);
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    /**
     * Get the default properties of all items.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_PARENT,
                self::PROPERTY_TYPE,
                self::PROPERTY_SORT,
                self::PROPERTY_HIDDEN,
                self::PROPERTY_DISPLAY,
                self::PROPERTY_ICON_CLASS
            )
        );
    }

    /**
     * @return int
     * @deprecated Use Item::getDisplay() now
     */
    public function get_display()
    {
        return $this->getDisplay();
    }

    /**
     * @return int
     * @deprecated Use Item::getHidden() now
     */
    public function get_hidden()
    {
        return $this->getHidden();
    }

    /**
     * @return int
     * @deprecated Use Item::getParent() now
     */
    public function get_parent()
    {
        return $this->getParentId();
    }

    /**
     * @return int
     * @deprecated Use Item::getSort() now
     */
    public function get_sort()
    {
        return $this->getSort();
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'menu_item';
    }

    /**
     * @return bool
     * @deprecated Use Item::hadParentId() now
     */
    public function hasParent()
    {
        return $this->hasParentId();
    }

    /**
     * @return bool
     */
    public function hasParentId()
    {
        return $this->getParentId() != 0;
    }

    /**
     *
     * @return bool
     */
    public function isHidden()
    {
        return (bool) $this->getHidden();
    }

    /**
     *
     * @return bool
     * @deprecated Use Item::isHidden() now
     */
    public function is_hidden()
    {
        return $this->isHidden();
    }

    /**
     * @param int $display
     */
    public function setDisplay($display = self::DISPLAY_ICON)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY, $display);
    }

    /**
     * @param int $hidden
     */
    public function setHidden($hidden = 0)
    {
        $this->setDefaultProperty(self::PROPERTY_HIDDEN, $hidden);
    }

    /**
     * @param string $iconClass
     */
    public function setIconClass($iconClass = '')
    {
        $this->setDefaultProperty(self::PROPERTY_ICON_CLASS, $iconClass);
    }

    /**
     * @param int $parent
     */
    public function setParentId($parent)
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT, $parent);
    }

    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }

    /**
     * @param int $display
     *
     * @deprecated Use Item::setDisplay() now
     */
    public function set_display($display = self::DISPLAY_ICON)
    {
        $this->setDisplay($display);
    }

    /**
     * @param int $hidden
     *
     * @deprecated User Item::setHidden() now
     */
    public function set_hidden($hidden = 0)
    {
        $this->setHidden($hidden);
    }

    /**
     * @param int $parent
     *
     * @deprecated Use Item::setParent() now
     */
    public function set_parent($parent)
    {
        $this->setParentId($parent);
    }

    /**
     * @param int $sort
     *
     * @deprecated Use Item::setSort() now
     */
    public function set_sort($sort)
    {
        $this->setSort($sort);
    }

    /**
     * @return bool
     */
    public function showIcon()
    {
        return $this->getDisplay() == self::DISPLAY_BOTH || $this->getDisplay() == self::DISPLAY_ICON;
    }

    /**
     * @return bool
     */
    public function showTitle()
    {
        return $this->getDisplay() == self::DISPLAY_TEXT || $this->getDisplay() == self::DISPLAY_BOTH;
    }

    /**
     * @return bool
     * @deprecated Use Item::showIcon() now
     */
    public function show_icon()
    {
        return $this->showIcon();
    }

    /**
     * @return bool
     * @deprecated Use Item::showTitle() now
     */
    public function show_title()
    {
        return $this->showTitle();
    }
}
