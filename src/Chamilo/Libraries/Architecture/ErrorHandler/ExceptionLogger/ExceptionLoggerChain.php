<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

/**
 * Exception Logger that chains other exception loggers
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerChain implements ExceptionLoggerInterface
{

    /**
     * The exception loggers
     * 
     * @var ExceptionLoggerInterface[]
     */
    protected $exceptionLoggers;

    /**
     * ExceptionLoggerChain constructor.
     * 
     * @param ExceptionLoggerInterface[] $exceptionLoggers
     *
     * @throws \Exception
     */
    public function __construct(array $exceptionLoggers)
    {
        if (empty($exceptionLoggers))
        {
            throw new \Exception(
                'You must provide at least one exception logger that implements ExceptionLoggerInterface');
        }
        
        foreach ($exceptionLoggers as $exceptionLogger)
        {
            if (! $exceptionLogger instanceof ExceptionLoggerInterface)
            {
                throw new \Exception(
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
     * @param int $exceptionLevel
     * @param string $file
     * @param int $line
     */
    public function logException($exception, $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, $file = null, $line = 0)
    {
        foreach ($this->exceptionLoggers as $exceptionLogger)
        {
            $exceptionLogger->logException($exception, $exceptionLevel, $file, $line);
        }
    }
}