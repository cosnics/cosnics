<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Configuration;

/**
 * Interface for classes that build exception loggers
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 */
interface ExceptionLoggerBuilderInterface
{
    /**
     * ExceptionLoggerBuilderInterface constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration);

    /**
     * Creates the exception logger
     *
     * @return ExceptionLoggerInterface
     */
    public function createExceptionLogger();
}