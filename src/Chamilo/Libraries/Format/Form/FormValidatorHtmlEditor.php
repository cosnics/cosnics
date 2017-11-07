<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormValidatorHtmlEditor
{
    const SETTING_TOOLBAR = 'toolbar';
    const SETTING_LANGUAGE = 'language';
    const SETTING_THEME = 'theme';
    const SETTING_WIDTH = 'width';
    const SETTING_HEIGHT = 'height';
    const SETTING_COLLAPSE_TOOLBAR = 'collapse_toolbar';
    const SETTING_CONFIGURATION = 'configuration';
    const SETTING_FULL_PAGE = 'full_page';
    const SETTING_ENTER_MODE = 'enter_mode';
    const SETTING_SHIFT_ENTER_MODE = 'shift_enter_mode';
    const SETTING_TEMPLATES = 'templates';

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $form;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $label;

    /**
     *
     * @var boolean
     */
    private $required;

    /**
     *
     * @var string[]
     */
    private $attributes;

    /**
     *
     * @var string[]
     */
    private $options;

    /**
     *
     * @param string $name
     * @param string $label
     * @param boolean $required
     * @param string[] $options
     * @param string[] $attributes
     */
    public function __construct($name, $label, $required = true, $options = array(), $attributes = array())
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->options = $options;

        if (! array_key_exists('class', $attributes))
        {
            $attributes['class'] = 'html_editor';
        }

        $this->attributes = $attributes;
    }

    public function add()
    {
        $form = $this->get_form();
        $element = $this->create();

        $form->addElement($element);
        $form->applyFilter($this->get_name(), 'trim');

        if ($this->get_required())
        {
            $form->addRule($this->get_name(), Translation::get('ThisFieldIsRequired'), 'required');
        }
    }

    /**
     *
     * @return \HTML_QuickForm_textarea
     */
    public function create()
    {
        $form = $this->get_form();
        $form->register_html_editor($this->name);
        return $form->createElement('textarea', $this->name, $this->label, $this->attributes);
    }

    /**
     *
     * @return \HTML_QuickForm_textarea
     */
    public function render()
    {
        $formValidator = new FormValidator('test');
        return $formValidator->createElement('textarea', $this->name, $this->label, $this->attributes)->toHtml();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function get_form()
    {
        return $this->form;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function set_form($form)
    {
        $this->form = $form;
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function get_label()
    {
        return $this->label;
    }

    /**
     *
     * @param string $label
     */
    public function set_label($label)
    {
        $this->label = $label;
    }

    /**
     *
     * @return string[]
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     *
     * @param string[] $attributes
     */
    public function set_attributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     *
     * @return string[]
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     *
     * @param string[] $options
     */
    public function set_options($options)
    {
        $this->options = $options;
    }

    /**
     *
     * @param string $variable
     * @return string
     */
    public function get_option($variable)
    {
        if (isset($this->options[$variable]))
        {
            return $this->options[$variable];
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @param string $variable
     * @param string $value
     */
    public function set_option($variable, $value)
    {
        $this->options[$variable] = $value;
    }

    /**
     *
     * @return boolean
     */
    public function get_required()
    {
        return $this->required;
    }

    /**
     *
     * @param boolean $required
     */
    public function set_required($required)
    {
        $this->required = $required;
    }

    /**
     *
     * @param string $type
     * @param string $name
     * @param string $label
     * @param boolean $required
     * @param string[] $options
     * @param string[] $attributes
     * @return \Chamilo\Libraries\Format\Form\FormValidatorHtmlEditor
     */
    public static function factory($type, $name, $label, $required = true, $options = array(), $attributes = array())
    {
        $class = __NAMESPACE__ . '\\' . 'FormValidator' .
             StringUtilities::getInstance()->createString($type)->upperCamelize() . 'HtmlEditor';

        if (class_exists($class))
        {
            $options = FormValidatorHtmlEditorOptions::factory($type, $options);

            if ($options)
            {
                return new $class($name, $label, $required, $options, $attributes);
            }
        }
    }
}
