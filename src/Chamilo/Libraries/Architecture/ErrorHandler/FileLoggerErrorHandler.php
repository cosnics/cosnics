<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler;

/**
 * Logs errors to a file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FileLoggerErrorHandler implements ErrorHandlerInterface
{
    /**
     * The path to the log directory
     *
     * @var string
     */
    protected $logPath;

    /**
     * FileLoggerErrorHandler constructor.
     *
     * @param $logPath
     */
    public function __construct($logPath)
    {
        $this->logPath = $logPath;
    }

    /**
     * Handles a fatal error
     *
     * @param string $fatalErrorMessage
     * @param string $file
     * @param string $line
     */
    public function handleFatalError($fatalErrorMessage, $file, $line)
    {
        $logFile = $this->logPath . DIRECTORY_SEPARATOR . 'FatalErrors.log';
        $fileHandler = fopen($logFile, 'a');

        $message = date('[d/m/Y - H:i:s] ', time()) . $fatalErrorMessage . ' - FILE: ' . $file . ' - LINE: ' . $line;

        fwrite($fileHandler, $message . "\n");
        fclose($fileHandler);
    }

    /**
     * Handles a catchable error
     *
     * @param int $errorNumber
     * @param string $errorString
     * @param string $file
     * @param int $line
     */
    public function handleError($errorNumber, $errorString, $file, $line)
    {
        // TODO: Implement handleError() method.
    }

    /**
     * Handles an exception
     *
     * @param \Exception $exception
     */
    public function handleException($exception)
    {
        // TODO: Implement handleException() method.
    }
}