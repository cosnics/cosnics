<?php
namespace Chamilo\Configuration\Form\Storage\DataClass;

use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Element extends DataClass
{
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    const PROPERTY_DYNAMIC_FORM_ID = 'dynamic_form_id';

    const PROPERTY_NAME = 'name';

    const PROPERTY_REQUIRED = 'required';

    const PROPERTY_TYPE = 'type';

    const TYPE_CHECKBOX = 3;

    const TYPE_HTMLEDITOR = 2;

    const TYPE_RADIO_BUTTONS = 4;

    const TYPE_SELECT_BOX = 5;

    const TYPE_TEXTBOX = 1;

    private $options;

    public function DynamicForm($defaultProperties)
    {
        parent::__construct($defaultProperties);
        // $this->options = [];
    }

    public function add_options($options)
    {
        if (!is_array($options))
        {
            $options = array($options);
        }
        foreach ($options as $option)
        {
            $this->options[] = $option;
        }
    }

    public function create()
    {
        $this->set_display_order(DataManager::select_next_dynamic_form_element_order($this->get_dynamic_form_id()));

        return parent::create();
    }

    /**
     * @param $type
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public static function getTypeGlyph($type)
    {
        switch ($type)
        {
            case self::TYPE_TEXTBOX :
                return new FontAwesomeGlyph('font', [], null, 'fas');
            case self::TYPE_HTMLEDITOR :
                return new FontAwesomeGlyph('code', [], null, 'fas');
            case self::TYPE_RADIO_BUTTONS :
                return new FontAwesomeGlyph('check-circle', [], null, 'fas');
            case self::TYPE_CHECKBOX :
                return new FontAwesomeGlyph('check-square', [], null, 'fas');
            case self::TYPE_SELECT_BOX :
                return new FontAwesomeGlyph('caret-square-down', [], null, 'fas');
        }
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties of all user course categories.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_DYNAMIC_FORM_ID, self::PROPERTY_NAME, self::PROPERTY_TYPE, self::PROPERTY_REQUIRED,
                self::PROPERTY_DISPLAY_ORDER
            )
        );
    }

    public function get_display_order()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
    }

    public function get_dynamic_form_id()
    {
        return $this->get_default_property(self::PROPERTY_DYNAMIC_FORM_ID);
    }

    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    public function get_option($index)
    {
        return $this->options[$index];
    }

    public function get_options()
    {
        if (!$this->options)
        {
            $this->load_options();
        }

        return $this->options;
    }

    public function set_options($options)
    {
        $this->options = $options;
    }

    public function get_required()
    {
        return $this->get_default_property(self::PROPERTY_REQUIRED);
    }

    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
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

    public static function get_types()
    {
        return array(
            Translation::get('Textbox') => self::TYPE_TEXTBOX, Translation::get('HtmlEditor') => self::TYPE_HTMLEDITOR,
            Translation::get('Checkbox') => self::TYPE_CHECKBOX,
            Translation::get('RadioButtons') => self::TYPE_RADIO_BUTTONS,
            Translation::get('SelectBox') => self::TYPE_SELECT_BOX
        );
    }

    public function load_options()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Option::class, Option::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new StaticConditionVariable($this->get_id())
        );
        $options = DataManager::retrieve_dynamic_form_element_options($condition);
        $this->set_options($options);

        return $this->options;
    }

    public function set_display_order($display_order)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function set_dynamic_form_id($dynamic_form_id)
    {
        $this->set_default_property(self::PROPERTY_DYNAMIC_FORM_ID, $dynamic_form_id);
    }

    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    public function set_required($required)
    {
        $this->set_default_property(self::PROPERTY_REQUIRED, $required);
    }

    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'configuration_form_element';
    }
}
