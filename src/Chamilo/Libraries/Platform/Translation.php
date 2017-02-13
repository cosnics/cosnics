<?php
namespace Chamilo\Libraries\Platform;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;

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
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

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
     * A list with reserved words that can not be used as a variable in the translation files
     *
     * @var string[]
     */
    private $reservedWords = array('true', 'false', 'on', 'off', 'null', 'yes', 'no', 'none');

    /**
     *
     * @var \Chamilo\Libraries\Platform\TranslationCacheService
     */
    private $translationCacheService;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param string $languageIsoCode
     */
    public function __construct(ClassnameUtilities $classnameUtilities, PathBuilder $pathBuilder,
        TranslationCacheService $translationCacheService, $languageIsoCode)
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->pathBuilder = $pathBuilder;
        $this->translationCacheService = $translationCacheService;
        $this->languageIsocode = $languageIsoCode;

        $this->strings = array();
        $this->strings[$this->languageIsocode] = $this->getTranslationCacheService()->getForIdentifier(
            $this->languageIsocode);
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
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function setPathBuilder($pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            $classnameUtilities = ClassnameUtilities::getInstance();
            $pathBuilder = new PathBuilder($classnameUtilities);
            $fileConfigurationConsulter = new ConfigurationConsulter(
                new FileConfigurationLoader(new FileConfigurationLocator($pathBuilder)));

            self::$instance = new static(
                $classnameUtilities,
                $pathBuilder,
                new TranslationCacheService(),
                $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'general', 'language')));
        }

        return static::$instance;
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
        $instance = self::getInstance();

        return $instance->getTranslation($variable, $parameters, $context, $isocode);
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\TranslationCacheService
     */
    public function getTranslationCacheService()
    {
        return $this->translationCacheService;
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
        $this->determineDefaultTranslationContext();

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
            if (count(explode('\\', self::$calledClass)) > 1)
            {
                $context = $this->getClassnameUtilities()->getNamespaceFromClassname(self::$calledClass);
            }
        }

        if (! isset($strings[$language]))
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
                return $variable;
            }
        }

        return $value;
    }

    /**
     *
     * @param string $language
     */
    private function loadCache($language)
    {
        $this->strings[$language] = $this->getTranslationCacheService()->getForIdentifier($language);
    }

    protected function determineDefaultTranslationContext()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $counter = 1;

        /** If called by deprecated method get, the actual class is deeper in the stack trace */
        do
        {
            $class = $backtrace[$counter]['class'];
            $counter++;
        }
        while($class == __CLASS__);

        self::$calledClass = $class;
    }
}
