<?php

namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\File\Path;

/*
 * Builds the FileExceptionLogger class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{

    /**
     * ExceptionLoggerBuilderInterface constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
    }

    /**
     * Creates the exception logger
     *
     * @return ExceptionLoggerInterface
     */
    public function createExceptionLogger()
    {
        return new FileExceptionLogger(Path::getInstance()->getLogPath());
    }
}