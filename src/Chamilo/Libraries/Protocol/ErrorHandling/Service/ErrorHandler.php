<?php
namespace Chamilo\Libraries\Protocol\ErrorHandling\Service;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Exception;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * Manages the error handler, the exception handler and the shutdown function
 *
 * @package Chamilo\Libraries\Protocol\ErrorHandling\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ErrorHandler
{
    public function __construct(
        protected ExceptionLoggerInterface $exceptionLogger, protected Translator $translator,
        protected ThemePathBuilder $themeSystemPathBuilder
    )
    {
    }

    protected function displayGeneralErrorPage(): void
    {
        $path = $this->themeSystemPathBuilder->getTemplatePath(Manager::CONTEXT, false) . 'Error.html.tpl';

        $template = file_get_contents($path);

        $variables = [
            'error_code' => 500,
            'error_title' => $this->getTranslation('FatalErrorTitle'),
            'error_content' => $this->getTranslation('FatalErrorContent'),
            'return_button_content' => $this->getTranslation('ReturnToPreviousPage')
        ];

        foreach ($variables as $variable => $value) {
            $template = str_replace('{ ' . $variable . ' }', $value, $template);
        }

        echo $template;
    }

    protected function getTranslation(
        string $variable, array $parameters = [], string $context = Manager::CONTEXT
    ): string
    {
        return $this->translator->trans($variable, $parameters, $context);
    }

    public function handleError(int $errorNumber, string $errorString, string $file, int $line): bool
    {
        $exceptionTypes = [
            E_USER_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR,
            E_USER_WARNING => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING,
            E_USER_NOTICE => ExceptionLoggerInterface::EXCEPTION_LEVEL_WARNING,
            E_RECOVERABLE_ERROR => ExceptionLoggerInterface::EXCEPTION_LEVEL_ERROR
        ];

        if (!array_key_exists($errorNumber, $exceptionTypes)) {
            return true;
        }

        $exceptionLevel = $exceptionTypes[$errorNumber];

        $this->exceptionLogger->logException(new Exception($errorString), $exceptionLevel, $file, $line);

        return true;
    }

    public function handleException(Throwable $exception): void
    {
        $this->exceptionLogger->logException($exception, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
        $this->displayGeneralErrorPage();
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();

        $allowedErrors = [E_ERROR, E_COMPILE_ERROR];

        if (!is_null($error) && in_array($error['type'], $allowedErrors)) {
            $this->exceptionLogger->logException(
                new Exception($error['message'] . '. File: ' . $error['file'] . '. Line: ' . $error['line'] . '.'),
                ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR, $error['file'], $error['line']
            );

            $this->displayGeneralErrorPage();
        }
    }

    /**
     * Registers the error handler, the exception handler and the shutdown function
     */
    public function registerErrorHandlers(): void
    {
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);

        register_shutdown_function([$this, 'handleShutdown']);
    }
}