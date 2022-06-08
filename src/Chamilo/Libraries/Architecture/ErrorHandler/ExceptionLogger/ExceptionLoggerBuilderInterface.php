<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;

/**
 * Interface for classes that build exception loggers
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ExceptionLoggerBuilderInterface
{

    public function __construct(ConfigurationConsulter $configurationConsulter);

    public function createExceptionLogger(): ExceptionLoggerInterface;
}