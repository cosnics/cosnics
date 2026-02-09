<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Exception;
use Throwable;

/**
 * Exception Logger that chains other exception loggers
 *
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExceptionLoggerChain implements ExceptionLoggerInterface
{
    /**
     * @var \Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface[]
     */
    protected array $exceptionLoggers;

    /**
     * @param \Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerInterface[] $exceptionLoggers
     *
     * @throws \Exception
     */
    public function __construct(array $exceptionLoggers)
    {
        if (empty($exceptionLoggers)) {
            throw new Exception(
                'You must provide at least one exception logger that implements ExceptionLoggerInterface'
            );
        }

        foreach ($exceptionLoggers as $exceptionLogger) {
            if (!$exceptionLogger instanceof ExceptionLoggerInterface) {
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

    public function addJavascriptExceptionLogger(PageHeaders $pageConfiguration): void
    {
        foreach ($this->exceptionLoggers as $exceptionLogger) {
            $exceptionLogger->addJavascriptExceptionLogger($pageConfiguration);
        }
    }

    public function logException(
        Throwable $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    ): void
    {
        foreach ($this->exceptionLoggers as $exceptionLogger) {
            $exceptionLogger->logException($exception, $exceptionLevel, $file, $line);
        }
    }
}