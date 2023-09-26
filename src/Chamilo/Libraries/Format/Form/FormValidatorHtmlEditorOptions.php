<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * The combination of options available for the FormValidatorHtmlEditor Should be implemented for each specific editor
 * to translate the generic option values
 *
 * @package Chamilo\Libraries\Format\Form
 * @author  Scaramanga
 */
class FormValidatorHtmlEditorOptions
{
    /**
     * Whether or not the toolbar should be collapse by default
     */
    public const OPTION_COLLAPSE_TOOLBAR = 'toolbarStartupExpanded';

    /**
     * Path to the editors configuration file
     */
    public const OPTION_CONFIGURATION = 'customConfig';

    /**
     * Whether or not the content of the editor should be treated as a standalone page
     */
    public const OPTION_FULL_PAGE = 'fullPage';

    /**
     * The height of the editor in pixels
     */
    public const OPTION_HEIGHT = 'height';

    /**
     * Name of the language to be used for the editor
     */
    public const OPTION_LANGUAGE = 'language';

    public const OPTION_RENDER_RESOURCE_INLINE = 'render_resource_inline';

    public const OPTION_SKIN = 'skin';

    /**
     * Path to available templates for the editor
     */
    public const OPTION_TEMPLATES = 'templates_files';

    /**
     * The name of the toolbar set e.g.
     * Basic, Wiki, Assessment
     */
    public const OPTION_TOOLBAR = 'toolbar';

    /**
     * The width of the editor in pixels or percent
     */
    public const OPTION_WIDTH = 'width';

    /**
     * @var string[]
     */
    private $options;

    /**
     * @param string[] $options
     */
    public function __construct($options)
    {
        $this->options = $options;
        $this->set_defaults();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function format_for_javascript($value)
    {
        if (is_bool($value))
        {
            if ($value === true)
            {
                return 'true';
            }
            else
            {
                return 'false';
            }
        }
        elseif (is_int($value))
        {
            return $value;
        }
        elseif (is_array($value))
        {
            $elements = [];

            foreach ($value as $element)
            {
                $elements[] = self::format_for_javascript($element);
            }

            return '[' . implode(',', $elements) . ']';
        }
        else
        {
            return '\'' . $value . '\'';
        }
    }

    /**
     * @return string[]
     */
    public function get_mapping()
    {
        return array_combine($this->get_option_names(), $this->get_option_names());
    }

    /**
     * Get a specific option's value or null if the option isn't set
     *
     * @param string $variable
     *
     * @return mixed the option's value
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
     * Returns the names of all available options
     *
     * @return string[] The option names
     */
    public function get_option_names()
    {
        return [
            self::OPTION_COLLAPSE_TOOLBAR,
            self::OPTION_CONFIGURATION,
            self::OPTION_FULL_PAGE,
            self::OPTION_LANGUAGE,
            self::OPTION_TEMPLATES,
            self::OPTION_TOOLBAR,
            self::OPTION_SKIN,
            self::OPTION_HEIGHT,
            self::OPTION_WIDTH,
            self::OPTION_RENDER_RESOURCE_INLINE
        ];
    }

    /**
     * Gets all options
     *
     * @return string[] The options
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     * @param bool $value
     *
     * @return bool
     */
    public function process_toolbarStartupExpanded($value)
    {
        if ($value === true)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Process the generic options into editor specific ones
     *
     * @return string
     */
    public function render_options()
    {
        $javascript = [];
        $available_options = $this->get_option_names();
        $mapping = $this->get_mapping();

        foreach ($available_options as $available_option)
        {
            if (key_exists($available_option, $mapping))
            {
                $value = $this->get_option($available_option);

                if (isset($value))
                {
                    $processing_function = 'process_' . $available_option;
                    if (method_exists($this, $processing_function))
                    {
                        $value = call_user_func([$this, $processing_function], $value);
                    }

                    $javascript[] =
                        '			' . $mapping[$available_option] . ' : ' . $this->format_for_javascript($value);
                }
            }
        }

        return implode(",\n", $javascript);
    }

    public function set_defaults()
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
         * @var \Chamilo\Libraries\Platform\ChamiloRequest $request
         */
        $request = $container->get(ChamiloRequest::class);

        $application = $request->query->get('application');
        $app_sys_path = $systemPathBuilder->getPluginPath($application) . 'HtmlEditor/CkeditorInstanceConfig.js';

        if (file_exists($app_sys_path))
        {
            $path = $webPathBuilder->getPluginPath($application) . 'HtmlEditor/CkeditorInstanceConfig.js';
        }
        else
        {
            $path = $webPathBuilder->getPluginPath(StringUtilities::LIBRARIES) . 'HtmlEditor/CkeditorInstanceConfig.js';
        }

        $available_options = $this->get_option_names();

        foreach ($available_options as $available_option)
        {
            $value = $this->get_option($available_option);
            if (!isset($value))
            {
                switch ($available_option)
                {
                    case self::OPTION_LANGUAGE :
                        $editor_lang = Translation::getInstance()->getLanguageIsocode();
                        $this->set_option($available_option, $editor_lang);
                        break;
                    case self::OPTION_TOOLBAR :
                        $this->set_option($available_option, 'Basic');
                        break;
                    case self::OPTION_FULL_PAGE:
                    case self::OPTION_COLLAPSE_TOOLBAR :
                        $this->set_option($available_option, false);
                        break;
                    case self::OPTION_WIDTH :
                        $this->set_option($available_option, '100%');
                        break;
                    case self::OPTION_HEIGHT :
                        $this->set_option($available_option, 200);
                        break;
                    case self::OPTION_RENDER_RESOURCE_INLINE :
                        $this->set_option($available_option, true);
                        break;
                    case self::OPTION_SKIN :
                        $this->set_option($available_option, 'moono-lisa');
                        break;
                    case self::OPTION_CONFIGURATION :
                        $this->set_option($available_option, $path);
                        break;
                }
            }
        }
    }

    /**
     * Sets a specific option
     *
     * @param string $variable
     * @param mixed $value
     */
    public function set_option($variable, $value)
    {
        $this->options[$variable] = $value;
    }

    /**
     * Set the options
     *
     * @param string[] $options
     */
    public function set_options($options)
    {
        $this->options = $options;
    }
}
