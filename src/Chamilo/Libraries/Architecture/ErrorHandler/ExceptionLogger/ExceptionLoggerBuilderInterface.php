<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Platform\Session\SessionUtilities;

/**
 * Interface for classes that build exception loggers
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ExceptionLoggerBuilderInterface
{

    public function __construct(ConfigurationConsulter $configurationConsulter, SessionUtilities $sessionUtilities);

    public function createExceptionLogger(): ExceptionLoggerInterface;
}