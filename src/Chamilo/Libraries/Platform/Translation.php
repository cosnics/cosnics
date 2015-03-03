<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

/**
 * $Id: translation.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.translation
 */
class Translation
{

    /**
     * Instance of this class for the singleton pattern
     *
     * @var \Chamilo\Libraries\Platform\Translation
     */
    private static $instance;

    /**
     *
     * @var string
     */
    private static $called_class;

    /**
     *
     * @var string[]
     */
    private static $recently_added;

    /**
     * Language strings defined in the language-files.
     * Stored as an associative array.
     *
     * @var string[]
     */
    private $strings;

    /**
     * The language we're currently translating too
     *
     * @var string
     */
    private $language;

    /**
     * The application we're currently translating
     *
     * @var string
     */
    private $application;

    /**
     * To determine wether we should show the variable in a tooltip window or not (used for translation purposes)
     *
     * @var boolean
     */
    private $show_variable_in_translation;

    /**
     * Determines whether or not to use the caching
     *
     * @var boolean
     */
    private $use_caching = true;

    /**
     * A list with reserved words that can not be used as a variable in the translation files
     *
     * @var string[]
     */
    private $reserved_words = array('true', 'false', 'on', 'off', 'null', 'yes', 'no', 'none');

    /**
     * Constructor
     *
     * @param string $language
     */
    private function __construct($language = null)
    {
        if (is_null($language))
        {
            $this->language = \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'platform_language');
            $this->show_variable_in_translation = \Chamilo\Configuration\Configuration :: get(
                'Chamilo\Core\Admin',
                'show_variable_in_translation');
            $this->use_caching = ! \Chamilo\Configuration\Configuration :: get(
                'Chamilo\Core\Admin',
                'write_new_variables_to_translation_file');
        }
        else
        {
            $this->language = $language;
        }

        $this->strings = array();

        if ($this->use_caching)
        {
            $cache_file = Path :: getInstance()->getCachePath() . 'translation/' . $this->language;

            if (file_exists($cache_file))
            {
                $cached_translations = file_get_contents($cache_file);
                if ($cached_translations)
                {
                    $instance->strings[$this->language] = unserialize($cached_translations);
                }
            }
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     *
     * @param string $variable
     * @param string[] $parameters (always use capital letters) Example: Translation :: get('UserCount', array('COUNT'
     *        =>
     *        $usercount)); $lang['user']['UserCount'] = There are {COUNT} users on the system;
     * @return string
     */
    public static function get($variable, $parameters = array(), $context = null, $isocode = null)
    {
        $instance = self :: get_instance();

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        self :: $called_class = $backtrace[1]['class'];

        $translation = $instance->translate($variable, $context, $isocode);

        if (empty($parameters))
        {
            return $translation;
        }
        else
        {
            return strtr($translation, $parameters);
        }
    }

    /**
     *
     * @return string
     */
    public static function get_language()
    {
        $instance = self :: get_instance();
        return $instance->language;
    }

    /**
     *
     * @param string $language
     */
    public static function set_language($language)
    {
        $instance = self :: get_instance();
        $instance->language = $language;
    }

    /**
     *
     * @return string
     */
    public static function get_application()
    {
        $instance = self :: get_instance();
        return $instance->application;
    }

    /**
     *
     * @param string $application
     */
    public static function set_application($application)
    {
        $instance = self :: get_instance();
        $instance->application = $application;
    }

    /**
     *
     * @param string $variable
     * @param string $context
     * @param string $isocode
     * @return string
     */
    public function translate($variable, $context = null, $isocode = null)
    {
        $instance = self :: get_instance();
        if (is_null($isocode))
        {
            $language = $instance->language;
        }
        else
        {
            $language = $isocode;
        }
        $strings = & $instance->strings;

        $value = null;

        if (! $context)
        {
            if (count(explode('\\', self :: $called_class)) > 1)
            {
                $context = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(self :: $called_class);
            }
        }

        if (! isset($strings[$language]) && $this->use_caching)
        {
            self :: load_cache($language);
        }

        if (! isset($strings[$language][$context]))
        {
            $instance->add_context_internationalization($language, $context);
        }

        if (isset($strings[$language][$context][$variable]))
        {
            $value = $strings[$language][$context][$variable];
        }

        if (! $value || $value == '' || $value == ' ')
        {
            // TODO: This has a performance impact, but keeps the translations working until Translation calls nog
            // longer need to be "magically" routed
            $parent_context = ClassnameUtilities :: getInstance()->getNamespaceParent($context);

            if ($parent_context)
            {
                $value = $this->translate($variable, $parent_context, $isocode);
            }
            else
            {

                if (is_null($value) && ! $this->use_caching &&
                     ! array_key_exists($variable, $strings[$language][$context]) &&
                     count($strings[$language][$context]) > 0)
                {
                    $this->add_variable_to_context_internationalization($language, $context, $variable);
                }

                if (\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'hide_dcda_markup'))
                {
                    return $variable;
                }
                else
                {
                    return '[CDA context={' . $context . '}]' . $variable . '[/CDA]';
                }
            }
        }

        if ($this->show_variable_in_translation)
        {
            return '<span title="' . $context . ' - ' . $variable . '">' . $value . '</span>';
        }

        return $value;
    }

    /**
     *
     * @param string $language
     * @param string $context
     */
    public static function add_context_internationalization($language, $context)
    {
        $path = Path :: getInstance()->namespaceToFullPath($context) . '/Resources/I18n/' . $language . '.i18n';

        if (! is_readable($path))
        {
            return;
        }

        $strings = parse_ini_file($path);

        $instance = self :: get_instance();
        $instance->strings[$language][$context] = $strings;

        if ($instance->use_caching)
        {
            self :: cache($language);
        }
    }

    /**
     *
     * @param string $language
     */
    public static function cache($language)
    {
        $instance = self :: get_instance();
        $strings = $instance->strings[$language];
        Filesystem :: write_to_file(
            Path :: getInstance()->getCachePath() . 'translation/' . $language,
            serialize($strings));
    }

    /**
     *
     * @param string $language
     */
    public static function load_cache($language)
    {
        $instance = self :: get_instance();
        $cache_file = Path :: getInstance()->getCachePath() . 'translation/' . $language;

        if (file_exists($cache_file))
        {
            $cached_translations = file_get_contents($cache_file);
            if ($cached_translations)
            {
                $translations = unserialize($cached_translations);
                if ($translations != false)
                {
                    $instance->strings[$language] = $translations;
                }
            }
        }
    }

    /**
     *
     * @param string $language
     * @param string $context
     * @param string $variable
     */
    private function add_variable_to_context_internationalization($language, $context, $variable)
    {
        if (in_array(strtolower($variable), $this->reserved_words))
        {
            continue;
        }

        if (! in_array($variable, self :: $recently_added[$language][$context]))
        {
            $path = Path :: getInstance()->namespaceToFullPath($context) . '/resources/i18n/' . $language . '.i18n';
            if (is_writable(dirname($path)))
            {
                if (! $handle = fopen($path, 'a'))
                {
                    return;
                }

                $string = "\n" . $variable . ' = ""';

                // Write $somecontent to our opened file
                if (fwrite($handle, $string) === FALSE)
                {
                    return;
                }

                fclose($handle);

                self :: $recently_added[$language][$context][] = $variable;
            }
        }
    }
}
