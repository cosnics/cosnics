<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Platform\Session\SessionUtilities;

/**
 * Builds the FileExceptionLogger class
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{
    protected ConfigurationConsulter $configurationConsulter;

    protected SessionUtilities $sessionUtilities;

    public function __construct(ConfigurationConsulter $configurationConsulter, SessionUtilities $sessionUtilities)
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * @throws \Exception
     */
    public function createExceptionLogger(): FileExceptionLogger
    {

        $configurablePathBuilder = new ConfigurablePathBuilder(
            $this->getConfigurationConsulter()->getSetting(array('Chamilo\Configuration', 'storage'))
        );

        return new FileExceptionLogger($configurablePathBuilder->getLogPath());
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

}