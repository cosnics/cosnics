<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\BaseHeader;
use Exception;

/**
 * Exception Logger that chains other exception loggers
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerChain implements ExceptionLoggerInterface
{

    /**
     * The exception loggers
     *
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface[]
     */
    protected $exceptionLoggers;

    /**
     * ExceptionLoggerChain constructor.
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface[] $exceptionLoggers
     * @throws \Exception
     */
    public function __construct(array $exceptionLoggers)
    {
        if (empty($exceptionLoggers))
        {
            throw new Exception(
                'You must provide at least one exception logger that implements ExceptionLoggerInterface');
        }

        foreach ($exceptionLoggers as $exceptionLogger)
        {
            if (! $exceptionLogger instanceof ExceptionLoggerInterface)
            {
                throw new Exception(
                    sprintf(
                        'The given exception logger does not implement ExceptionLoggerInterface (%s)',
                        get_class($exceptionLogger)));
            }
        }

        $this->exceptionLoggers = $exceptionLoggers;
    }

    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param integer $exceptionLevel
     * @param string $file
     * @param integer $line
     */
    public function logException($exception, $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, $file = null, $line = 0)
    {
        foreach ($this->exceptionLoggers as $exceptionLogger)
        {
            $exceptionLogger->logException($exception, $exceptionLevel, $file, $line);
        }
    }

    /**
     * Adds an exception logger for javascript to the header
     *
     * @param \Chamilo\Libraries\Format\Structure\BaseHeader $header
     */
    public function addJavascriptExceptionLogger(BaseHeader $header)
    {
        foreach ($this->exceptionLoggers as $exceptionLogger)
        {
            $exceptionLogger->addJavascriptExceptionLogger($header);
        }
    }
}