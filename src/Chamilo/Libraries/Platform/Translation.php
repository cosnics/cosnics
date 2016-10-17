<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\Platform
 */
class Translation
{

    /**
     *
     * @var \Chamilo\Configuration\Configuration
     */
    private $configuration;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @var \Chamilo\Libraries\File\Path
     */
    private $pathUtilities;

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
    private static $calledClass;

    /**
     *
     * @var string[]
     */
    private static $recentlyAdded;

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
    private $languageIsocode;

    /**
     * To determine wether we should show the variable in a tooltip window or not (used for translation purposes)
     *
     * @var boolean
     */
    private $showTranslationVariable;

    /**
     * Determines whether or not to use the caching
     *
     * @var boolean
     */
    private $usesCaching = true;

    /**
     * A list with reserved words that can not be used as a variable in the translation files
     *
     * @var string[]
     */
    private $reservedWords = array('true', 'false', 'on', 'off', 'null', 'yes', 'no', 'none');

    /**
     *
     * @var \Chamilo\Libraries\Platform\TranslationCacheService
     */
    private $cacheService;

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    private function __construct(Configuration $configuration, ClassnameUtilities $classnameUtilities,
        Path $pathUtilities)
    {
        $this->configuration = $configuration;
        $this->classnameUtilities = $classnameUtilities;
        $this->pathUtilities = $pathUtilities;

        $this->languageIsocode = $this->configuration->get_setting(array('Chamilo\Core\Admin', 'platform_language'));
        $this->showTranslationVariable = $this->configuration->get_setting(
            array('Chamilo\Core\Admin', 'show_variable_in_translation'));
        $this->usesCaching = ! $this->configuration->get_setting(
            array('Chamilo\Core\Admin', 'write_new_variables_to_translation_file'));

        $this->strings = array();

        if ($this->usesCaching)
        {
            $this->strings[$this->languageIsocode] = $this->getCacheService()->getForIdentifier($this->languageIsocode);
        }
    }

    /**
     *
     * @return string
     */
    public function getLanguageIsocode()
    {
        return $this->languageIsocode;
    }

    /**
     *
     * @param string $languageIsocode
     */
    public function setLanguageIsocode($languageIsocode)
    {
        $this->languageIsocode = $languageIsocode;
    }

    /**
     *
     * @return \Chamilo\Configuration\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param \Chamilo\Configuration\Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities()
    {
        return $this->classnameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities($classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\Path
     */
    public function getPathUtilities()
    {
        return $this->pathUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\Path $pathUtilities
     */
    public function setPathUtilities($pathUtilities)
    {
        $this->pathUtilities = $pathUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            $configuraton = \Chamilo\Configuration\Configuration :: get_instance();
            $classnameUtilities = ClassnameUtilities :: getInstance();
            $pathUtilities = Path :: getInstance();
            self :: $instance = new static($configuraton, $classnameUtilities, $pathUtilities);
        }

        return static :: $instance;
    }

    /**
     *
     * @deprecated Use getTranslation() now
     * @param string $variable
     * @param string[] $parameters
     * @return string
     *
     */
    public static function get($variable, $parameters = array(), $context = null, $isocode = null)
    {
        $instance = self :: getInstance();

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        self :: $calledClass = $backtrace[1]['class'];

        return $instance->getTranslation($variable, $parameters, $context, $isocode);
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\TranslationCacheService
     */
    public function getCacheService()
    {
        if (! isset($this->cacheService))
        {
            $this->cacheService = new TranslationCacheService();
        }

        return $this->cacheService;
    }

    /**
     *
     * @param string $variable
     * @param string[] $parameters
     * @param string $context
     * @param string $isocode
     * @return string
     */
    public function getTranslation($variable, $parameters = array(), $context = null, $isocode = null)
    {
        $translation = $this->doTranslation($variable, $context, $isocode);

        if (empty($parameters))
        {
            return $translation;
        }
        else
        {
            $translationMap = array();

            foreach ($parameters as $key => $value)
            {
                $translationMap['{' . $key . '}'] = $value;
            }

            return strtr($translation, $translationMap);
        }
    }

    /**
     *
     * @param string $variable
     * @param string $context
     * @param string $isocode
     * @return string
     */
    private function doTranslation($variable, $context = null, $isocode = null)
    {
        if (is_null($isocode))
        {
            $language = $this->getLanguageIsocode();
        }
        else
        {
            $language = $isocode;
        }
        $strings = & $this->strings;

        $value = null;

        if (! $context)
        {
            if (count(explode('\\', self :: $calledClass)) > 1)
            {
                $context = $this->getClassnameUtilities()->getNamespaceFromClassname(self :: $calledClass);
            }
        }

        if (! isset($strings[$language]) && $this->usesCaching)
        {
            $this->loadCache($language);
        }

        if (isset($strings[$language][$context][$variable]))
        {
            $value = $strings[$language][$context][$variable];
        }

        if (! $value || $value == '' || $value == ' ')
        {
            // TODO: This has a performance impact, but keeps the translations working until Translation calls nog
            // longer need to be "magically" routed
            $parent_context = $this->getClassnameUtilities()->getNamespaceParent($context);

            if ($parent_context)
            {
                $value = $this->doTranslation($variable, $parent_context, $isocode);
            }
            else
            {

                if (is_null($value) && ! $this->usesCaching &&
                     ! array_key_exists($variable, $strings[$language][$context]) &&
                     count($strings[$language][$context]) > 0)
                {
                    $this->writeLanguageVariable($language, $context, $variable);
                }

                if ($this->getConfiguration()->get_setting(array('Chamilo\Core\Admin', 'hide_dcda_markup')))
                {
                    return $variable;
                }
                else
                {
                    return '[CDA context={' . $context . '}]' . $variable . '[/CDA]';
                }
            }
        }

        if ($this->showTranslationVariable)
        {
            return '<span title="' . htmlentities($context . ' - ' . $variable) . '">' . $value . '</span>';
        }

        return $value;
    }

    /**
     *
     * @param string $language
     */
    private function loadCache($language)
    {
        $this->strings[$language] = $this->getCacheService()->getForIdentifier($language);
    }

    /**
     *
     * @param string $language
     * @param string $context
     * @param string $variable
     */
    private function writeLanguageVariable($language, $context, $variable)
    {
        if (in_array(strtolower($variable), $this->reservedWords))
        {
            return;
        }

        if (! in_array($variable, self :: $recentlyAdded[$language][$context]))
        {
            $path = $this->getPathUtilities()->namespaceToFullPath($context) . '/resources/i18n/' . $language . '.i18n';

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

                self :: $recentlyAdded[$language][$context][] = $variable;
            }
        }
    }
}
