<?php
namespace Chamilo\Configuration\Form\Storage\DataClass;

use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Option extends DataClass
{
    const PROPERTY_DYNAMIC_FORM_ELEMENT_ID = 'dynamic_form_element_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    public function get_dynamic_form_element_id()
    {
        return $this->get_default_property(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID);
    }

    public function set_dynamic_form_element_id($dynamic_form_element_id)
    {
        $this->set_default_property(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
    }

    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    public function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    public function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * Get the default properties of all user course categories.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, self :: PROPERTY_NAME, self :: PROPERTY_DISPLAY_ORDER));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    public function create()
    {
        $this->set_display_order(
            DataManager :: select_next_dynamic_form_element_option_order($this->get_dynamic_form_element_id()));
        return parent :: create();
    }
}
