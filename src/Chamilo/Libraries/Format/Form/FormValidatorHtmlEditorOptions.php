<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * The combination of options available for the FormValidatorHtmlEditor Should be implemented for each specific editor
 * to translate the generic option values
 * 
 * @author Scaramanga
 */
abstract class FormValidatorHtmlEditorOptions
{

    /**
     *
     * @var Array The array containing all the options
     */
    private $options;
    const OPTION_SKIN = 'skin';
    
    /**
     * The name of the toolbar set e.g.
     * Basic, Wiki, Assessment
     */
    const OPTION_TOOLBAR = 'toolbar';
    
    /**
     * Name of the language to be used for the editor
     */
    const OPTION_LANGUAGE = 'language';
    
    /**
     * The width of the editor in pixels or percent
     */
    const OPTION_WIDTH = 'width';
    
    /**
     * The height of the editor in pixels
     */
    const OPTION_HEIGHT = 'height';
    
    /**
     * Whether or not the toolbar should be collapse by default
     */
    const OPTION_COLLAPSE_TOOLBAR = 'collapse_toolbar';
    
    /**
     * Path to the editors configuration file
     */
    const OPTION_CONFIGURATION = 'configuration';
    
    /**
     * Whether or not the content of the editor should be treated as a standalone page
     */
    const OPTION_FULL_PAGE = 'full_page';
    
    /**
     * Path to available templates for the editor
     */
    const OPTION_TEMPLATES = 'templates';

    /**
     *
     * @param Array $options
     */
    public function __construct($options)
    {
        $this->options = $options;
        $this->set_defaults();
    }

    /**
     * Returns the names of all available options
     * 
     * @return Array The option names
     */
    public function get_option_names()
    {
        return array(
            self::OPTION_COLLAPSE_TOOLBAR, 
            self::OPTION_CONFIGURATION, 
            self::OPTION_FULL_PAGE, 
            self::OPTION_LANGUAGE, 
            self::OPTION_TEMPLATES, 
            self::OPTION_TOOLBAR, 
            self::OPTION_SKIN, 
            self::OPTION_HEIGHT, 
            self::OPTION_WIDTH);
    }

    /**
     * Gets all options
     * 
     * @return Array The options
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     * Set the options
     * 
     * @param Array $options
     */
    public function set_options($options)
    {
        $this->options = $options;
    }

    /**
     * Get a specific option's value or null if the option isn't set
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
     * Sets a specific option
     * 
     * @param String $variable
     * @param mixed $value
     */
    public function set_option($variable, $value)
    {
        $this->options[$variable] = $value;
    }

    public function get_mapping()
    {
        return array_combine($this->get_option_names(), $this->get_option_names());
    }

    /**
     * Process the generic options into editor specific ones
     */
    public function render_options()
    {
        $javascript = array();
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
                        $value = call_user_func(array($this, $processing_function), $value);
                    }
                    
                    $javascript[] = '			' . $mapping[$available_option] . ' : ' . $this->format_for_javascript($value);
                }
            }
        }
        
        return implode(",\n", $javascript);
    }

    public function set_defaults()
    {
        $available_options = $this->get_option_names();
        
        foreach ($available_options as $available_option)
        {
            $value = $this->get_option($available_option);
            if (! isset($value))
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
                    case self::OPTION_COLLAPSE_TOOLBAR :
                        $this->set_option($available_option, false);
                        break;
                    
                    case self::OPTION_WIDTH :
                        $this->set_option($available_option, '100%');
                        break;
                    case self::OPTION_HEIGHT :
                        $this->set_option($available_option, 200);
                        break;
                    
                    case self::OPTION_FULL_PAGE :
                        $this->set_option($available_option, false);
                        break;
                }
            }
        }
    }

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
            $elements = array();
            
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
     *
     * @param String $type
     * @param Array $options
     * @return FormValidatorHtmlEditorOptions
     */
    public static function factory($type, $options = array())
    {
        $class = __NAMESPACE__ . '\\' . 'FormValidator' .
             StringUtilities::getInstance()->createString($type)->upperCamelize() . 'HtmlEditorOptions';
        
        if (class_exists($class))
        {
            return new $class($options);
        }
        else
        {
            return null;
        }
    }
}
