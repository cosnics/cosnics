<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

/**
 * Logs errors to a file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLogger implements ExceptionLoggerInterface
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
     *
     * @throws \Exception
     */
    public function __construct($logPath)
    {
        if (empty($logPath))
        {
            throw new \Exception('The given log path can not be empty');
        }

        if (!file_exists($logPath) || !is_dir($logPath) || !is_writable($logPath))
        {
            throw new \Exception(
                sprintf('The given log path either does not exist or is not a valid directory. (%s)', $logPath)
            );
        }

        $this->logPath = $logPath;
    }

    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param string $file
     * @param int $line
     */
    public function logException(\Exception $exception, $file = null, $line = 0)
    {
        $logFile = $this->logPath . DIRECTORY_SEPARATOR . 'FatalErrors.log';
        $fileHandler = fopen($logFile, 'a');

        $message = date('[d/m/Y - H:i:s] ', time()) . $exception->getMessage();

        if (!is_null($file))
        {
            $message .= ' - FILE: ' . $file . ' - LINE: ' . $line;
        }

        fwrite($fileHandler, $message . "\n");
        fclose($fileHandler);
    }
}