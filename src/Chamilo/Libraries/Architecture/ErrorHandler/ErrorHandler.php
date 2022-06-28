<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler;

use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Exception;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * Manages the error handler, the exception handler and the shutdown function
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ErrorHandler
{

    protected ExceptionLoggerInterface $exceptionLogger;

    protected ThemePathBuilder $themePathBuilder;

    protected Translator $translator;

    public function __construct(
        ExceptionLoggerInterface $exceptionLogger, Translator $translator, ThemePathBuilder $themePathBuilder
    )
    {
        $this->exceptionLogger = $exceptionLogger;
        $this->translator = $translator;
        $this->themePathBuilder = $themePathBuilder;
    }

    protected function displayGeneralErrorPage()
    {
        $path = $this->getThemePathBuilder()->getTemplatePath('Chamilo\Configuration', false) . 'Error.html.tpl';

        $template = file_get_contents($path);

        $variables = [
            'error_code' => 500,
            'error_title' => $this->getTranslation('FatalErrorTitle'),
            'error_content' => $this->getTranslation('FatalErrorContent'),
            'return_button_content' => $this->getTranslation('ReturnToPreviousPage')
        ];

        foreach ($variables as $variable => $value)
        {
            $template = str_replace('{ ' . $variable . ' }', $value, $template);
        }

        echo $template;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    public function setExceptionLogger(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    public function getThemePathBuilder(): ThemePathBuilder
    {
        return $this->themePathBuilder;
    }

    public function setThemePathBuilder(ThemePathBuilder $themePathBuilder)
    {
        $this->themePathBuilder = $themePathBuilder;
    }

    protected function getTranslation(string $variable, array $parameters = [], string $context = 'Chamilo\Configuration'
    ): string
    {
        return $this->getTranslator()->trans($variable, $parameters, $context);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function handleError(int $errorNumber, string $errorString, string $file, int $line): bool
    {
        $exceptionTypes = [
            E_USER_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR,
            E_USER_WARNING => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING,
            E_USER_NOTICE => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING,
            E_RECOVERABLE_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR
        ];

        if (!array_key_exists($errorNumber, $exceptionTypes))
        {
            return true;
        }

        $exceptionLevel = $exceptionTypes[$errorNumber];

        $this->getExceptionLogger()->logException(new Exception($errorString), $exceptionLevel, $file, $line);

        return true;
    }

    public function handleException(Throwable $exception)
    {
        $this->getExceptionLogger()->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
        $this->displayGeneralErrorPage();
    }

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
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);

        register_shutdown_function([$this, 'handleShutdown']);
    }
}