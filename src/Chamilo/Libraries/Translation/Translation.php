<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\Translation\Translator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Translation
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Try to use the Symfony Translator service whenever possible
 */
class Translation
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

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
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(ClassnameUtilities $classnameUtilities, Translator $translator)
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->translator = $translator;
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
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getLanguageIsocode()
    {
        return $this->getTranslator()->getLocale();
    }

    /**
     *
     * @param string $languageIsoCode
     */
    public function setLanguageIsocode($languageIsoCode)
    {
        $locale = $languageIsoCode . '_' . strtoupper($languageIsoCode);
        $this->getTranslator()->setLocale($locale);
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
            $translator = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                'symfony.component.translation.translator');

            self::$instance = new self($classnameUtilities, $translator);
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
        return self::getInstance()->getTranslation($variable, (array) $parameters, $context, $isocode);
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

        if (! $context)
        {
            if (count(explode('\\', self::$calledClass)) > 1)
            {
                $context = $this->getClassnameUtilities()->getNamespaceFromClassname(self::$calledClass);
            }
        }

        $parsedParameters = array();

        foreach ($parameters as $key => $value)
        {
            $parsedParameters['{' . $key . '}'] = $value;
        }

        return $this->doTranslation($variable, $parsedParameters, $context, $isocode);
    }

    /**
     *
     * @param string $variable
     * @param string[] $parameters
     * @param string $context
     * @param string $isocode
     * @return string
     */
    protected function doTranslation($variable, $parameters = array(), $context = null, $isocode = null)
    {
        $translation = $this->getTranslator()->trans($variable, (array) $parameters, $context, $isocode);

        if ($translation == $variable)
        {
            $parentContext = $this->getClassnameUtilities()->getNamespaceParent($context);

            if ($parentContext)
            {
                $translation = $this->doTranslation($variable, $parameters, $parentContext, $isocode);
            }
            else
            {
                return $variable;
            }
        }

        return $translation;
    }

    protected function determineDefaultTranslationContext()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $counter = 1;

        /**
         * If called by deprecated method get, the actual class is deeper in the stack trace
         */
        do
        {
            $class = $backtrace[$counter]['class'];
            $counter ++;
        }
        while ($class == __CLASS__);

        self::$calledClass = $class;
    }
}
