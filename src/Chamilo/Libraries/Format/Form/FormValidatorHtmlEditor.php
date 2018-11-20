<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

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
        $this->options = new FormValidatorHtmlEditorOptions($options);

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

        $form->addElement('html', implode(PHP_EOL, $this->add_pre_javascript_config()));

        $scripts = $this->get_includes();

        foreach ($scripts as $script)
        {
            if (! empty($script))
            {
                $form->addElement('html', $script);
            }
        }

        $form->addElement('html', implode(PHP_EOL, $this->get_javascript()));
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

        $html = array();

        $html[] = $formValidator->createElement('textarea', $this->name, $this->label, $this->attributes)->toHtml();
        $html[] = implode(PHP_EOL, $this->get_javascript());

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function add_pre_javascript_config()
    {
        $javascript = array();

        $javascript[] = '<script type="text/javascript">';
        $javascript[] = 'window.CKEDITOR_BASEPATH = "' .
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . '" + "HtmlEditor/Ckeditor/"';
            $javascript[] = '</script>';

            return $javascript;
    }

    /**
     *
     * @return string[]
     */
    public function get_javascript()
    {
        $javascript = array();

        $javascript[] = '<script type="text/javascript">';
        $javascript[] = 'var web_path = \'' . Path::getInstance()->getBasePath(true) . '\'';
        $javascript[] = '$(function ()';
        $javascript[] = '{';
        $javascript[] = '	$(document).ready(function ()';
        $javascript[] = '	{';
        $javascript[] = '         if(typeof $el == \'undefined\'){';
        $javascript[] = '           $el = new Array()';
        $javascript[] = '         }';
        $javascript[] = '	  $el.push($("textarea.html_editor[name=\'' . $this->get_name() . '\']").ckeditor({';
        $javascript[] = $this->get_options()->render_options();
        $javascript[] = '		}, function(){ $(document).trigger(\'ckeditor_loaded\'); }));';
        $javascript[] = '	}); ';
        $javascript[] = '});';
        $javascript[] = '</script>';

        return $javascript;
    }

    /**
     *
     * @return string[]
     */
    public function get_includes()
    {
        $configFile = Path::getInstance()->getBasePath() .
        '../web/Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorInstanceConfig.js';

        $timestamp = filemtime($configFile);

        $scripts = array();

        $scripts[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/Ckeditor/ckeditor.js');
        $scripts[] = '<script type="text/javascript">';
        $scripts[] = 'CKEDITOR.timestamp = "' . $timestamp . '";';
        $scripts[] = 'var web_path = \'' . Path::getInstance()->getBasePath(true) . '\';';
        $scripts[] = '</script>';
        $scripts[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/CkeditorGlobalConfig.js');
        $scripts[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HtmlEditor/Ckeditor/adapters/jquery.js');

        return $scripts;
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
}
