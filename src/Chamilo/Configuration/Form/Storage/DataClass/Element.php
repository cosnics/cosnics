<?php
namespace Chamilo\Configuration\Form\Storage\DataClass;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package configuration\form
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Element extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_DISPLAY_ORDER = 'display_order';
    public const PROPERTY_DYNAMIC_FORM_ID = 'dynamic_form_id';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_REQUIRED = 'required';
    public const PROPERTY_TYPE = 'type';

    public const TYPE_CHECKBOX = 3;
    public const TYPE_HTMLEDITOR = 2;
    public const TYPE_RADIO_BUTTONS = 4;
    public const TYPE_SELECT_BOX = 5;
    public const TYPE_TEXTBOX = 1;

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
            $options = [$options];
        }
        foreach ($options as $option)
        {
            $this->options[] = $option;
        }
    }

    public function create(): bool
    {
        $this->set_display_order(DataManager::select_next_dynamic_form_element_order($this->get_dynamic_form_id()));

        return parent::create();
    }

    /**
     * Get the default properties of all user course categories.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_DYNAMIC_FORM_ID,
                self::PROPERTY_NAME,
                self::PROPERTY_TYPE,
                self::PROPERTY_REQUIRED,
                self::PROPERTY_DISPLAY_ORDER
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_form_element';
    }

    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
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

    public static function getTypeName($type)
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

    public function get_display_order()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
    }

    public function get_dynamic_form_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_DYNAMIC_FORM_ID);
    }

    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
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

    public function get_required()
    {
        return $this->getDefaultProperty(self::PROPERTY_REQUIRED);
    }

    /**
     * @deprecated Use Element::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @deprecated Use Element::getTypeName() now
     */
    public static function get_type_name($type)
    {
        return self::getTypeName($type);
    }

    public static function get_types()
    {
        return [
            Translation::get('Textbox') => self::TYPE_TEXTBOX,
            Translation::get('HtmlEditor') => self::TYPE_HTMLEDITOR,
            Translation::get('Checkbox') => self::TYPE_CHECKBOX,
            Translation::get('RadioButtons') => self::TYPE_RADIO_BUTTONS,
            Translation::get('SelectBox') => self::TYPE_SELECT_BOX
        ];
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

    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    public function set_display_order($display_order)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function set_dynamic_form_id($dynamic_form_id)
    {
        $this->setDefaultProperty(self::PROPERTY_DYNAMIC_FORM_ID, $dynamic_form_id);
    }

    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    public function set_options($options)
    {
        $this->options = $options;
    }

    public function set_required($required)
    {
        $this->setDefaultProperty(self::PROPERTY_REQUIRED, $required);
    }

    /**
     * @deprecated Use Element::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }
}
