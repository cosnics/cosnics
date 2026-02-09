<?php
namespace Chamilo\Libraries\Protocol\Error\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Throwable;

/**
 * Interface for services that can handle errors
 *
 * @package Chamilo\Libraries\Protocol\Error\Architecture\Interface
 */
interface ExceptionLoggerInterface
{
    public const EXCEPTION_LEVEL_ERROR = 2;
    public const EXCEPTION_LEVEL_FATAL_ERROR = 3;
    public const EXCEPTION_LEVEL_WARNING = 1;

    /**
     * Adds an exception logger for javascript to the header
     */
    public function addJavascriptExceptionLogger(PageHeaders $pageConfiguration);

    /**
     * Logs an exception
     */
    public function logException(
        Throwable $exception, int $exceptionLevel = self::EXCEPTION_LEVEL_ERROR, ?string $file = null, int $line = 0
    );
}