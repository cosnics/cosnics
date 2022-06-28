<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Throwable;

/**
 * Interface for services that can handle errors
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 */
interface ExceptionLoggerInterface
{
    public const EXCEPTION_LEVEL_ERROR = 2;
    public const EXCEPTION_LEVEL_FATAL_ERROR = 3;
    public const EXCEPTION_LEVEL_WARNING = 1;

    /**
     * Adds an exception logger for javascript to the header
     */
    public function addJavascriptExceptionLogger(PageConfiguration $pageConfiguration);

    /**
     * Logs an exception
     */
    public function logException(
        Throwable $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    );
}