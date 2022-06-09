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

    private static string $calledClass;

    private static ?Translation $instance = null;

    private ClassnameUtilities $classnameUtilities;

    private Translator $translator;

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

    protected function doTranslation(
        string $variable, ?array $parameters = [], ?string $context = null, ?string $isocode = null
    ): string
    {
        $translation = $this->getTranslator()->trans($variable, $parameters, $context, $isocode);

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
     * @throws \ReflectionException
     * @throws \Exception
     * @deprecated Use getTranslation() now
     */
    public static function get(
        string $variable, ?array $parameters = [], ?string $context = null, ?string $isocode = null
    ): string
    {
        return self::getInstance()->getTranslation($variable, (array) $parameters, $context, $isocode);
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * @throws \Exception
     */
    public static function getInstance(): Translation
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
     * @deprecated Use Translator->getLocale() now
     */
    public function getLanguageIsocode(): string
    {
        return $this->getTranslator()->getLocale();
    }

    /**
     * @throws \ReflectionException
     */
    public function getTranslation(
        string $variable, ?array $parameters = [], ?string $context = null, ?string $isocode = null
    ): string
    {
        if (is_null($parameters))
        {
            $parameters = [];
        }

        $this->determineDefaultTranslationContext();

        if (!$context)
        {
            if (count(explode('\\', self::$calledClass)) > 1)
            {
                $context = $this->getClassnameUtilities()->getNamespaceFromClassname(self::$calledClass);
            }
        }

        $parsedParameters = [];

        foreach ($parameters as $key => $value)
        {
            $parsedParameters['{' . $key . '}'] = $value;
        }

        return $this->doTranslation($variable, $parsedParameters, $context, $isocode);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function setLanguageIsocode(string $languageIsoCode)
    {
        $this->getTranslator()->setLocale($languageIsoCode);
    }
}
