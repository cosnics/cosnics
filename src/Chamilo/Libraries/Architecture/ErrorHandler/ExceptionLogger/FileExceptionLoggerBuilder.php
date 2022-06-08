<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

/**
 * Builds the FileExceptionLogger class
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{
    protected ConfigurationConsulter $configurationConsulter;

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
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

}