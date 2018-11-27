<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemTitle extends DataClass
{
    const PROPERTY_ISOCODE = 'isocode';

    const PROPERTY_ITEM_ID = 'item_id';

    const PROPERTY_SORT = 'sort';

    const PROPERTY_TITLE = 'title';

    /**
     * @return string
     */
    public function getIsocode()
    {
        return $this->get_default_property(self::PROPERTY_ISOCODE);
    }

    /**
     * @return integer
     */
    public function getItemId()
    {
        return $this->get_default_property(self::PROPERTY_ITEM_ID);
    }

    /**
     * @return integer
     */
    public function getSort()
    {
        return $this->get_default_property(self::PROPERTY_SORT);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->get_default_property(self::PROPERTY_TITLE);
    }

    /**
     * Get the default properties of all items.
     *
     * @param string[] $extendedPropertyNames
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extendedPropertyNames = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_ITEM_ID, self::PROPERTY_TITLE, self::PROPERTY_SORT, self::PROPERTY_ISOCODE)
        );
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
     * @return integer
     * @deprecated Use ItemTitle::getItemId() now
     */
    public function get_item_id()
    {
        return $this->getItemId();
    }

    /**
     * @return integer
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
     */
    public function setIsocode($isocode)
    {
        $this->set_default_property(self::PROPERTY_ISOCODE, $isocode);
    }

    /**
     * @param integer $itemIdentifier
     */
    public function setItemId($itemIdentifier)
    {
        $this->set_default_property(self::PROPERTY_ITEM_ID, $itemIdentifier);
    }

    /**
     * @param integer $sort
     */
    public function setSort($sort)
    {
        $this->set_default_property(self::PROPERTY_SORT, $sort);
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->set_default_property(self::PROPERTY_TITLE, $title);
    }

    /**
     * @param string $isocode
     *
     * @deprecated Use ItemTitle::setIsocode() now
     */
    public function set_isocode($isocode)
    {
        $this->setIsocode($isocode);
    }

    /**
     * @param integer $itemIdentifier
     *
     * @deprecated Use ItemTitle::setItemId() now
     */
    public function set_item_id($itemIdentifier)
    {
        $this->setItemId($itemIdentifier);
    }

    /**
     * @param integer $sort
     *
     * @deprecated Use ItemTitle::setSort() now
     */
    public function set_sort($sort)
    {
        $this->setSort($sort);
    }

    /**
     * @param string $title
     *
     * @deprecated Use ItemTitle::setTitle() now
     */
    public function set_title($title)
    {
        $this->setTitle($title);
    }
}
