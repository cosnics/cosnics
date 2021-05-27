<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler;

use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * Manages the error handler, the exception handler and the shutdown function
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ErrorHandler
{

    /**
     * The Exception Logger
     *
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    protected $themePathBuilder;

    /**
     * ErrorHandler constructor.
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     */
    public function __construct(
        ExceptionLoggerInterface $exceptionLogger, Translator $translator, ThemePathBuilder $themePathBuilder
    )
    {
        $this->exceptionLogger = $exceptionLogger;
        $this->translator = $translator;
        $this->themePathBuilder = $themePathBuilder;
    }

    /**
     * Displays a general error page
     */
    protected function displayGeneralErrorPage()
    {
        $path = $this->getThemePathBuilder()->getTemplatePath('Chamilo\Configuration', false) . 'Error.html.tpl';

        $template = file_get_contents($path);

        $variables = array(
            'error_code' => 500,
            'error_title' => $this->getTranslation('FatalErrorTitle'),
            'error_content' => $this->getTranslation('FatalErrorContent'),
            'return_button_content' => $this->getTranslation('ReturnToPreviousPage')
        );

        foreach ($variables as $variable => $value)
        {
            $template = str_replace('{ ' . $variable . ' }', $value, $template);
        }

        echo $template;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function getExceptionLogger()
    {
        return $this->exceptionLogger;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder()
    {
        return $this->themePathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     */
    public function setThemePathBuilder(ThemePathBuilder $themePathBuilder)
    {
        $this->themePathBuilder = $themePathBuilder;
    }

    /**
     * Helper function for translations
     *
     * @param string $variable
     * @param string[] $parameters
     * @param string $context
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = [], $context = 'Chamilo\Configuration')
    {
        return $this->getTranslator()->trans($variable, $parameters, $context);
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
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * General error handler for (catchable) errors in PHP
     *
     * @param integer $errorNumber
     * @param string $errorString
     * @param string $file
     * @param integer $line
     *
     * @return boolean
     */
    public function handleError($errorNumber, $errorString, $file, $line)
    {
        $exceptionTypes = array(
            E_USER_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR,
            E_USER_WARNING => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING,
            E_USER_NOTICE => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING,
            E_RECOVERABLE_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR
        );

        if (!array_key_exists($errorNumber, $exceptionTypes))
        {
            return true;
        }

        $exceptionLevel = $exceptionTypes[$errorNumber];

        $this->getExceptionLogger()->logException(new Exception($errorString), $exceptionLevel, $file, $line);

        return true;
    }

    /**
     * General exception handler for exceptions in PHP
     *
     * @param \Exception $exception
     */
    public function handleException($exception)
    {
        $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
        $this->displayGeneralErrorPage();
    }

    /**
     * General shutdown handler for fatal errors in PHP
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        $allowedErrors = [E_ERROR, E_COMPILE_ERROR];

        if (!is_null($error) && in_array($error['type'], $allowedErrors))
        {
            $this->getExceptionLogger()->logException(
                new Exception($error['message'] . '. File: ' . $error['file'] . '. Line: ' . $error['line'] . '.'),
                ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR, $error['file'], $error['line']
            );

            $this->displayGeneralErrorPage();
        }
    }

    /**
     * Registers the error handler, the exception handler and the shutdown function
     */
    public function registerErrorHandlers()
    {
        set_exception_handler(array($this, 'handleException'));
        set_error_handler(array($this, 'handleError'));

        register_shutdown_function(array($this, 'handleShutdown'));
    }
}