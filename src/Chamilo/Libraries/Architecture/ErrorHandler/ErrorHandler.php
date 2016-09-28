<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler;

use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Manages the error handler, the exception handler and the shutdown function
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ErrorHandler
{
    /**
     * The Exception Logger
     * 
     * @var ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     * @var Translation
     */
    protected $translator;

    /**
     * ErrorHandlerManager constructor.
     *
     * @param ExceptionLoggerInterface $exceptionLogger
     * @param Translation $translator
     */
    public function __construct(ExceptionLoggerInterface $exceptionLogger, Translation $translator)
    {
        $this->exceptionLogger = $exceptionLogger;
        $this->translator = $translator;
    }

    /**
     * General shutdown handler for fatal errors in PHP
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        if(!is_null($error) && $error['type'] == E_ERROR)
        {
            $this->exceptionLogger->logException(new \Exception($error['message']), $error['file'], $error['line']);
            $this->displayGeneralErrorPage();
        }
    }

    /**
     * General error handler for (catchable) errors in PHP
     *
     * @param int $errorNumber
     * @param string $errorString
     * @param string $file
     * @param int $line
     *
     * @return bool
     */
    public function handleError($errorNumber, $errorString, $file, $line)
    {
        return Utilities::handle_error($errorNumber, $errorString, $file, $line);
    }

    /**
     * General exception handler for exceptions in PHP
     *
     * @param \Exception $exception
     */
    public function handleException($exception)
    {
        $this->exceptionLogger->logException($exception);
        $this->displayGeneralErrorPage();
    }

    /**
     * Registers the error handler, the exception handler and the shutdown function
     */
    public function registerErrorHandlers()
    {
        set_exception_handler(array($this, 'handleException'));
//        set_error_handler(array($this, 'handleError'));

        set_error_handler('\Chamilo\Libraries\Utilities\Utilities::handle_error');

        register_shutdown_function(array($this, 'handleShutdown'));
    }

    /**
     * Displays a general error page
     */
    protected function displayGeneralErrorPage()
    {
        $path = Path::getInstance()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/Templates/Error.html.tpl';
        $template = file_get_contents($path);
        
        $variables = array(
            'error_code' => 500,
            'error_title' => $this->getTranslation('FatalErrorTitle'),
            'error_content' => $this->getTranslation('FatalErrorContent'),
            'return_button_content' => $this->getTranslation('ReturnToPreviousPage')
        );

        foreach($variables as $variable => $value)
        {
            $template = str_replace('{ ' . $variable . ' }', $value, $template);
        }

        echo $template;
    }

    /**
     * Helper function for translations
     *
     * @param string $variable
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array(), $context = 'Chamilo\Configuration')
    {
        return $this->translator->getTranslation($variable, $parameters, $context);
    }
}