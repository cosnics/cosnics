<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Exception;
use Throwable;

/**
 * Exception Logger that chains other exception loggers
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerChain implements ExceptionLoggerInterface
{

    /**
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface[]
     */
    protected array $exceptionLoggers;

    /**
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface[] $exceptionLoggers
     *
     * @throws \Exception
     */
    public function __construct(array $exceptionLoggers)
    {
        if (empty($exceptionLoggers))
        {
            throw new Exception(
                'You must provide at least one exception logger that implements ExceptionLoggerInterface'
            );
        }

        foreach ($exceptionLoggers as $exceptionLogger)
        {
            if (!$exceptionLogger instanceof ExceptionLoggerInterface)
            {
                throw new Exception(
                    sprintf(
                        'The given exception logger does not implement ExceptionLoggerInterface (%s)',
                        get_class($exceptionLogger)
                    )
                );
            }
        }

        $this->exceptionLoggers = $exceptionLoggers;
    }

    public function addJavascriptExceptionLogger(PageConfiguration $pageConfiguration)
    {
        foreach ($this->exceptionLoggers as $exceptionLogger)
        {
            $exceptionLogger->addJavascriptExceptionLogger($pageConfiguration);
        }
    }

    public function logException(
        Throwable $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    )
    {
        foreach ($this->exceptionLoggers as $exceptionLogger)
        {
            $exceptionLogger->logException($exception, $exceptionLevel, $file, $line);
        }
    }
}