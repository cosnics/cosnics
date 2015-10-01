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
 * $Id: home_tab.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib
 */
class Tab extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
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
            array(self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_USER));
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
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_USER), 
            new StaticConditionVariable($this->get_user()));
        $this->set_sort(DataManager :: retrieve_next_value(self :: class_name(), self :: PROPERTY_SORT, $condition));
        return parent :: create();
    }

    public function delete()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_TAB), 
            new StaticConditionVariable($this->get_id()));
        $rows = DataManager :: retrieves(Row :: class_name(), $condition);
        
        while ($row = $rows->next_result())
        {
            if (! $row->delete())
            {
                return false;
            }
        }
        
        return parent :: delete();
    }

    public function can_be_deleted()
    {
        $blocks = DataManager :: retrieve_home_tab_blocks($this);
        
        while ($block = $blocks->next_result())
        {
            $context = $block->get_context();
            if ($context == 'Chamilo\Core\Admin' || $context == 'Chamilo\Core\User')
            {
                return false;
            }
        }
        
        return true;
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
        return array(new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_USER));
    }
}
