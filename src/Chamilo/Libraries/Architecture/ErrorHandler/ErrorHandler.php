<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler;

use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
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
     * ErrorHandlerManager constructor.
     *
     * @param ExceptionLoggerInterface $exceptionLogger
     */
    public function __construct(ExceptionLoggerInterface $exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
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
        Utilities::handle_exception($exception);
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