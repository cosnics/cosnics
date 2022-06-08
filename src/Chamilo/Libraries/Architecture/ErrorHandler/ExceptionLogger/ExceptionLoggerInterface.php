<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\BaseHeader;
use Exception;

/**
 * Interface for services that can handle errors
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 */
interface ExceptionLoggerInterface
{
    const EXCEPTION_LEVEL_ERROR = 2;
    const EXCEPTION_LEVEL_FATAL_ERROR = 3;
    const EXCEPTION_LEVEL_WARNING = 1;

    /**
     * Adds an exception logger for javascript to the header
     */
    public function addJavascriptExceptionLogger(BaseHeader $header);

    /**
     * Logs an exception
     */
    public function logException(
        Exception $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    );
}