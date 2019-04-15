<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Libraries\Format\Structure\BaseHeader;

/**
 * Interface for services that can handle errors
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 */
interface ExceptionLoggerInterface
{
    /**
     * Exception types
     */
    const EXCEPTION_LEVEL_WARNING = 1;
    const EXCEPTION_LEVEL_ERROR = 2;
    const EXCEPTION_LEVEL_FATAL_ERROR = 3;

    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param integer $exceptionLevel
     * @param string $file
     * @param integer $line
     */
    public function logException($exception, $exceptionLevel = self::EXCEPTION_LEVEL_FATAL_ERROR, $file = null, $line = 0);

    /**
     * Adds an exception logger for javascript to the header
     *
     * @param \Chamilo\Libraries\Format\Structure\BaseHeader $header
     */
    public function addJavascriptExceptionLogger(BaseHeader $header);
}