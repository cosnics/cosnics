<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

/**
 * Logs errors to a file
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
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
     * @throws \Exception
     */
    public function __construct($logPath)
    {
        if (empty($logPath))
        {
            throw new \Exception('The given log path can not be empty');
        }

        if (! file_exists($logPath) || ! is_dir($logPath) || ! is_writable($logPath))
        {
            throw new \Exception(
                sprintf('The given log path either does not exist or is not a valid directory. (%s)', $logPath));
        }

        $this->logPath = $logPath;
    }

    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param int $exceptionLevel
     * @param string $file
     * @param int $line
     */
    public function logException($exception, $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, $file = null, $line = 0)
    {
        if ($exceptionLevel == self::EXCEPTION_LEVEL_WARNING)
        {
            return;
        }

        $logFile = $this->logPath . DIRECTORY_SEPARATOR . 'FatalErrors.log';
        $fileHandler = fopen($logFile, 'a');

        $type = $this->determineExceptionLevelString($exceptionLevel);

        $message = date('[d/m/Y - H:i:s] ', time()) . ' - [' . $type . '] ' . $exception->getMessage();

        if (! is_null($file))
        {
            $message .= ' - FILE: ' . $file . ' - LINE: ' . $line;
        }

        fwrite($fileHandler, $message . "\n");
        fclose($fileHandler);
    }

    /**
     * Determines the exception level string
     *
     * @param int $exceptionLevel
     *
     * @return string
     */
    protected function determineExceptionLevelString($exceptionLevel = self::EXCEPTION_LEVEL_ERROR)
    {
        switch ($exceptionLevel)
        {
            case self::EXCEPTION_LEVEL_WARNING :
                return 'WARNING';
            case self::EXCEPTION_LEVEL_ERROR :
                return 'ERROR';
            case self::EXCEPTION_LEVEL_FATAL_ERROR :
                return 'FATAL';
            default :
                return '[ERROR]';
        }
    }
}