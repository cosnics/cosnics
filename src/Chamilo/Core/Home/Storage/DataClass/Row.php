<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: home_row.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib
 */
class Row extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_TAB = 'tab_id';
    const PROPERTY_USER = 'user_id';

    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent :: __construct($default_properties = $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    /**
     * Get the default properties of all user course categories.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_TAB, self :: PROPERTY_USER));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
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

    public function get_tab()
    {
        return $this->get_default_property(self :: PROPERTY_TAB);
    }

    public function set_tab($tab)
    {
        $this->set_default_property(self :: PROPERTY_TAB, $tab);
    }

    public function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    public function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    public function create()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_TAB), 
            new StaticConditionVariable($this->get_tab()));
        $this->set_sort(DataManager :: retrieve_next_value(self :: class_name(), self :: PROPERTY_SORT, $condition));
        return parent :: create();
    }

    public function delete()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ROW), 
            new StaticConditionVariable($this->get_id()));
        $columns = DataManager :: retrieves(Column :: class_name(), $condition);
        
        while ($column = $columns->next_result())
        {
            if (! $column->delete())
            {
                return false;
            }
        }
        
        return parent :: delete();
    }

    /**
     *
     * @see \libraries\storage\DisplayOrderDataClassListenerSupport::get_display_order_property()
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_SORT);
    }

    /**
     *
     * @see \libraries\storage\DisplayOrderDataClassListenerSupport::get_display_order_context_properties()
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_TAB));
    }
}
