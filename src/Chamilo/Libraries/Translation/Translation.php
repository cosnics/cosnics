<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Translation
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @deprecated Try to use the Symfony Translator service whenever possible
 */
class Translation
{

    /**
     * Instance of this class for the singleton pattern
     *
     * @var \Chamilo\Libraries\Translation\Translation
     */
    private static $instance;

    /**
     *
     * @var string
     */
    private static $calledClass;

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
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(ClassnameUtilities $classnameUtilities, Translator $translator)
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->translator = $translator;
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

    /**
     *
     * @param string $variable
     * @param string[] $parameters
     * @param string $context
     * @param string $isocode
     *
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

    /**
     *
     * @param string $variable
     * @param string[] $parameters
     *
     * @return string
     *
     * @deprecated Use getTranslation() now
     */
    public static function get($variable, $parameters = array(), $context = null, $isocode = null)
    {
        return self::getInstance()->getTranslation($variable, (array) $parameters, $context, $isocode);
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
     * @return \Chamilo\Libraries\Translation\Translation
     * @throws \Exception
     */
    static public function getInstance()
    {
        if (is_null(static::$instance))
        {
            $classnameUtilities = ClassnameUtilities::getInstance();
            /**
             * @var \Symfony\Component\Translation\Translator $translator
             */
            $translator = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                'Symfony\Component\Translation\Translator'
            );

            self::$instance = new self($classnameUtilities, $translator);
        }

        return static::$instance;
    }

    /**
     *
     * @return string
     * @deprecated Use Translator->getLocale() now
     */
    public function getLanguageIsocode()
    {
        return $this->getTranslator()->getLocale();
    }

    /**
     *
     * @param string $variable
     * @param string[] $parameters
     * @param string $context
     * @param string $isocode
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getTranslation($variable, $parameters = array(), $context = null, $isocode = null)
    {
        $this->determineDefaultTranslationContext();

        if (!$context)
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

    /**
     *
     * @param string $languageIsoCode
     */
    public function setLanguageIsocode($languageIsoCode)
    {
        $this->getTranslator()->setLocale($languageIsoCode);
    }
}
