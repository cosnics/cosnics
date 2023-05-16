<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemTitle extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ISOCODE = 'isocode';
    public const PROPERTY_ITEM_ID = 'item_id';
    public const PROPERTY_SORT = 'sort';
    public const PROPERTY_TITLE = 'title';

    /**
     * Get the default properties of all items.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_ITEM_ID, self::PROPERTY_TITLE, self::PROPERTY_SORT, self::PROPERTY_ISOCODE]
        );
    }

    /**
     * @return string
     */
    public function getIsocode()
    {
        return $this->getDefaultProperty(self::PROPERTY_ISOCODE);
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ITEM_ID);
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'menu_item_title';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getDefaultProperty(self::PROPERTY_TITLE);
    }

    /**
     * @return string
     * @deprecated Use ItemTitle::getIsocode() now
     */
    public function get_isocode()
    {
        return $this->getIsocode();
    }

    /**
     * @return int
     * @deprecated Use ItemTitle::getItemId() now
     */
    public function get_item_id()
    {
        return $this->getItemId();
    }

    /**
     * @return int
     * @deprecated Use ItemTitle::getSort() now
     */
    public function get_sort()
    {
        return $this->getSort();
    }

    /**
     * @return string
     * @deprecated Use ItemTitle::getTitle() now
     */
    public function get_title()
    {
        return $this->getTitle();
    }

    /**
     * @param string $isocode
     *
     * @throws \Exception
     */
    public function setIsocode($isocode)
    {
        $this->setDefaultProperty(self::PROPERTY_ISOCODE, $isocode);
    }

    /**
     * @param $itemIdentifier
     *
     * @throws \Exception
     */
    public function setItemId($itemIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_ITEM_ID, $itemIdentifier);
    }

    /**
     * @param int $sort
     *
     * @throws \Exception
     */
    public function setSort($sort)
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }

    /**
     * @param string $title
     *
     * @throws \Exception
     */
    public function setTitle($title)
    {
        $this->setDefaultProperty(self::PROPERTY_TITLE, $title);
    }

    /**
     * @param string $isocode
     *
     * @throws \Exception
     * @deprecated Use ItemTitle::setIsocode() now
     */
    public function set_isocode($isocode)
    {
        $this->setIsocode($isocode);
    }

    /**
     * @param int $itemIdentifier
     *
     * @throws \Exception
     * @deprecated Use ItemTitle::setItemId() now
     */
    public function set_item_id($itemIdentifier)
    {
        $this->setItemId($itemIdentifier);
    }

    /**
     * @param int $sort
     *
     * @throws \Exception
     * @deprecated Use ItemTitle::setSort() now
     */
    public function set_sort($sort)
    {
        $this->setSort($sort);
    }

    /**
     * @param string $title
     *
     * @throws \Exception
     * @deprecated Use ItemTitle::setTitle() now
     */
    public function set_title($title)
    {
        $this->setTitle($title);
    }
}
