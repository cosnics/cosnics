<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

/**
 * Interface for services that can handle errors
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler
 */
interface ExceptionLoggerInterface
{
    /**
     * Logs an exception
     *
     * @param \Exception $exception
     * @param string $file
     * @param int $line
     */
    public function logException(\Exception $exception, $file = null, $line = 0);
}