<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemTitle extends DataClass
{
    const PROPERTY_ITEM_ID = 'item_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_ISOCODE = 'isocode';

    private $titles;

    /**
     * Get the default properties of all items.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_ITEM_ID, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_ISOCODE));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    public function get_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_ITEM_ID);
    }

    public function set_item_id($item_id)
    {
        $this->set_default_property(self :: PROPERTY_ITEM_ID, $item_id);
    }

    public function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    public function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    public function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    public function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    public function get_isocode()
    {
        return $this->get_default_property(self :: PROPERTY_ISOCODE);
    }

    public function set_isocode($isocode)
    {
        $this->set_default_property(self :: PROPERTY_ISOCODE, $isocode);
    }

    public function create()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_ITEM_ID),
            new StaticConditionVariable($this->get_item_id()));
        $sort = DataManager :: retrieve_next_value(self :: class_name(), self :: PROPERTY_SORT, $condition);

        $this->set_sort($sort);

        $success = parent :: create($this);
        if (! $success)
        {
            return false;
        }

        return true;
    }
}
