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
    const DISPLAY_BOTH = 3;
    const DISPLAY_ICON = 1;
    const DISPLAY_TEXT = 2;

    /**
     * Properties
     */
    const PROPERTY_DISPLAY = 'display';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_ICON_CLASS = 'icon_class';
    const PROPERTY_PARENT = 'parent';
    const PROPERTY_SORT = 'sort';

    /**
     * Item types
     */
    const TYPE_APPLICATION = 1;
    const TYPE_CATEGORY = 3;
    const TYPE_LINK = 2;
    const TYPE_LINK_APPLICATION = 4;

    /**
     * @return integer
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
     * @return integer
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
     * @return integer
     */
    public function getParentId()
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT);
    }

    /**
     * @return integer
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
     * @return integer
     * @deprecated Use Item::getDisplay() now
     */
    public function get_display()
    {
        return $this->getDisplay();
    }

    /**
     * @return integer
     * @deprecated Use Item::getHidden() now
     */
    public function get_hidden()
    {
        return $this->getHidden();
    }

    /**
     * @return integer
     * @deprecated Use Item::getParent() now
     */
    public function get_parent()
    {
        return $this->getParentId();
    }

    /**
     * @return integer
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
     * @return boolean
     * @deprecated Use Item::hadParentId() now
     */
    public function hasParent()
    {
        return $this->hasParentId();
    }

    /**
     * @return boolean
     */
    public function hasParentId()
    {
        return $this->getParentId() != 0;
    }

    /**
     *
     * @return boolean
     */
    public function isHidden()
    {
        return (bool) $this->getHidden();
    }

    /**
     *
     * @return boolean
     * @deprecated Use Item::isHidden() now
     */
    public function is_hidden()
    {
        return $this->isHidden();
    }

    /**
     * @param integer $display
     */
    public function setDisplay($display = self::DISPLAY_ICON)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY, $display);
    }

    /**
     * @param integer $hidden
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
     * @param integer $parent
     */
    public function setParentId($parent)
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT, $parent);
    }

    /**
     * @param integer $sort
     */
    public function setSort($sort)
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }

    /**
     * @param integer $display
     *
     * @deprecated Use Item::setDisplay() now
     */
    public function set_display($display = self::DISPLAY_ICON)
    {
        $this->setDisplay($display);
    }

    /**
     * @param integer $hidden
     *
     * @deprecated User Item::setHidden() now
     */
    public function set_hidden($hidden = 0)
    {
        $this->setHidden($hidden);
    }

    /**
     * @param integer $parent
     *
     * @deprecated Use Item::setParent() now
     */
    public function set_parent($parent)
    {
        $this->setParentId($parent);
    }

    /**
     * @param integer $sort
     *
     * @deprecated Use Item::setSort() now
     */
    public function set_sort($sort)
    {
        $this->setSort($sort);
    }

    /**
     * @return boolean
     */
    public function showIcon()
    {
        return $this->getDisplay() == self::DISPLAY_BOTH || $this->getDisplay() == self::DISPLAY_ICON;
    }

    /**
     * @return boolean
     */
    public function showTitle()
    {
        return $this->getDisplay() == self::DISPLAY_TEXT || $this->getDisplay() == self::DISPLAY_BOTH;
    }

    /**
     * @return boolean
     * @deprecated Use Item::showIcon() now
     */
    public function show_icon()
    {
        return $this->showIcon();
    }

    /**
     * @return boolean
     * @deprecated Use Item::showTitle() now
     */
    public function show_title()
    {
        return $this->showTitle();
    }
}
