<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;

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
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(ConfigurationConsulter $configurationConsulter);

    /**
     * Creates the exception logger
     * 
     * @return ExceptionLoggerInterface
     */
    public function createExceptionLogger();
}