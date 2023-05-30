<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormValidatorHtmlEditor
{
    public const SETTING_COLLAPSE_TOOLBAR = 'collapse_toolbar';
    public const SETTING_CONFIGURATION = 'configuration';
    public const SETTING_ENTER_MODE = 'enter_mode';
    public const SETTING_FULL_PAGE = 'full_page';
    public const SETTING_HEIGHT = 'height';
    public const SETTING_LANGUAGE = 'language';
    public const SETTING_SHIFT_ENTER_MODE = 'shift_enter_mode';
    public const SETTING_TEMPLATES = 'templates';
    public const SETTING_THEME = 'theme';
    public const SETTING_TOOLBAR = 'toolbar';
    public const SETTING_WIDTH = 'width';

    /**
     * @var string[]
     */
    private $attributes;

    /**
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $form;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $options;

    /**
     * @var bool
     */
    private $required;

    /**
     * @param string $name
     * @param string $label
     * @param bool $required
     * @param string[] $options
     * @param string[] $attributes
     */
    public function __construct($name, $label, $required = true, $options = [], $attributes = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->options = new FormValidatorHtmlEditorOptions($options);

        if (!array_key_exists('class', $attributes))
        {
            $attributes['class'] = 'html_editor';
        }

        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function render()
    {
        $formValidator = new FormValidator('test');

        $html = [];

        $html[] = $formValidator->createElement('textarea', $this->name, $this->label, $this->attributes)->toHtml();
        $html[] = implode(PHP_EOL, $this->get_javascript());

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Exception
     */
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
     * @return string[]
     */
    public function add_pre_javascript_config()
    {
        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(WebPathBuilder::class);

        $javascript = [];

        $javascript[] = '<script>';
        $javascript[] = 'window.CKEDITOR_BASEPATH = "' . $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) .
            '" + "HtmlEditor/Ckeditor/"';
        $javascript[] = '</script>';

        return $javascript;
    }

    /**
     * @return \HTML_QuickForm_textarea
     */
    public function create()
    {
        $form = $this->get_form();

        $form->addElement('html', implode(PHP_EOL, $this->add_pre_javascript_config()));

        $scripts = $this->get_includes();

        foreach ($scripts as $script)
        {
            if (!empty($script))
            {
                $form->addElement('html', $script);
            }
        }

        $form->addElement('html', implode(PHP_EOL, $this->get_javascript()));
        $form->register_html_editor($this->name);

        return $form->createElement('textarea', $this->name, $this->label, $this->attributes);
    }

    /**
     * @return string[]
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function get_form()
    {
        return $this->form;
    }

    /**
     * @return string[]
     */
    public function get_includes()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        /**
         * @var \Chamilo\Libraries\File\SystemPathBuilder $systemPathBuilder
         */
        $systemPathBuilder = $container->get(SystemPathBuilder::class);
        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder = $container->get(WebPathBuilder::class);
        /**
         * @var \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
         */
        $resourceManager = $container->get(ResourceManager::class);

        $configFile = $systemPathBuilder->getBasePath() .
            '../web/Chamilo/Libraries/Resources/Plugin/HtmlEditor/CkeditorInstanceConfig.js';

        $timestamp = filemtime($configFile);

        $scripts = [];

        $scripts[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) . 'HtmlEditor/Ckeditor/ckeditor.js'
        );
        $scripts[] = '<script>';
        $scripts[] = 'CKEDITOR.timestamp = "' . $timestamp . '";';
        $scripts[] = 'var web_path = \'' . $webPathBuilder->getBasePath() . '\';';
        $scripts[] = '</script>';
        $scripts[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) . 'HtmlEditor/CkeditorGlobalConfig.js'
        );
        $scripts[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) . 'HtmlEditor/Ckeditor/adapters/jquery.js'
        );

        return $scripts;
    }

    /**
     * @return string[]
     */
    public function get_javascript()
    {
        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(WebPathBuilder::class);

        $javascript = [];

        $javascript[] = '<script>';
        $javascript[] = 'var web_path = \'' . $webPathBuilder->getBasePath() . '\'';
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
     * @return string
     */
    public function get_label()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param string $variable
     *
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
     * @return string[]
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function get_required()
    {
        return $this->required;
    }

    /**
     * @param string[] $attributes
     */
    public function set_attributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     */
    public function set_form($form)
    {
        $this->form = $form;
    }

    /**
     * @param string $label
     */
    public function set_label($label)
    {
        $this->label = $label;
    }

    /**
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $variable
     * @param string $value
     */
    public function set_option($variable, $value)
    {
        $this->options[$variable] = $value;
    }

    /**
     * @param string[] $options
     */
    public function set_options($options)
    {
        $this->options = $options;
    }

    /**
     * @param bool $required
     */
    public function set_required($required)
    {
        $this->required = $required;
    }
}
