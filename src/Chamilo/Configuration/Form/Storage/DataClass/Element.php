<?php
namespace Chamilo\Configuration\Form\Storage\DataClass;

use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Element extends DataClass
{
    const PROPERTY_DYNAMIC_FORM_ID = 'dynamic_form_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_REQUIRED = 'required';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const TYPE_TEXTBOX = 1;
    const TYPE_HTMLEDITOR = 2;
    const TYPE_CHECKBOX = 3;
    const TYPE_RADIO_BUTTONS = 4;
    const TYPE_SELECT_BOX = 5;

    private $options;

    public function DynamicForm($defaultProperties)
    {
        parent::__construct($defaultProperties);
        // $this->options = array();
    }

    public function get_dynamic_form_id()
    {
        return $this->get_default_property(self::PROPERTY_DYNAMIC_FORM_ID);
    }

    public function set_dynamic_form_id($dynamic_form_id)
    {
        $this->set_default_property(self::PROPERTY_DYNAMIC_FORM_ID, $dynamic_form_id);
    }

    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    public function get_required()
    {
        return $this->get_default_property(self::PROPERTY_REQUIRED);
    }

    public function set_required($required)
    {
        $this->set_default_property(self::PROPERTY_REQUIRED, $required);
    }

    public function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    public function set_display_order($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function get_options()
    {
        if (! $this->options)
            $this->load_options();
        return $this->options;
    }

    public function get_option($index)
    {
        return $this->options[$index];
    }

    public function set_options($options)
    {
        $this->options = $options;
    }

    public function add_options($options)
    {
        if (! is_array($options))
        {
            $options = array($options);
        }
        foreach ($options as $option)
        {
            $this->options[] = $option;
        }
    }

    public function load_options()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Option::class_name(), Option::PROPERTY_DYNAMIC_FORM_ELEMENT_ID), 
            new StaticConditionVariable($this->get_id()));
        $options = DataManager::retrieve_dynamic_form_element_options($condition);
        $this->set_options($options->as_array());
        return $this->options;
    }

    /**
     * Get the default properties of all user course categories.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_DYNAMIC_FORM_ID, 
                self::PROPERTY_NAME, 
                self::PROPERTY_TYPE, 
                self::PROPERTY_REQUIRED, 
                self::PROPERTY_DISPLAY_ORDER));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    public static function get_types()
    {
        return array(
            Translation::get('Textbox') => self::TYPE_TEXTBOX, 
            Translation::get('HtmlEditor') => self::TYPE_HTMLEDITOR, 
            Translation::get('Checkbox') => self::TYPE_CHECKBOX, 
            Translation::get('RadioButtons') => self::TYPE_RADIO_BUTTONS, 
            Translation::get('SelectBox') => self::TYPE_SELECT_BOX);
    }

    public static function get_type_name($type)
    {
        switch ($type)
        {
            case self::TYPE_TEXTBOX :
                return Translation::get('Textbox');
            case self::TYPE_HTMLEDITOR :
                return Translation::get('HtmlEditor');
            case self::TYPE_RADIO_BUTTONS :
                return Translation::get('RadioButtons');
            case self::TYPE_CHECKBOX :
                return Translation::get('Checkbox');
            case self::TYPE_SELECT_BOX :
                return Translation::get('SelectBox');
        }
    }

    public function create()
    {
        $this->set_display_order(DataManager::select_next_dynamic_form_element_order($this->get_dynamic_form_id()));
        return parent::create();
    }
}
