<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\File\Path;

/*
 * Builds the FileExceptionLogger class
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{

    /**
     *
     * @see \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerBuilderInterface::__construct()
     */
    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        // TODO Auto-generated method stub
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