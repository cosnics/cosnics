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
    private $configurationConsulter;

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Creates the exception logger
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\FileExceptionLogger
     * @throws \Exception
     */
    public function createExceptionLogger()
    {

        $configurablePathBuilder = new ConfigurablePathBuilder(
            $this->getConfigurationConsulter()->getSetting(array('Chamilo\Configuration', 'storage'))
        );

        return new FileExceptionLogger($configurablePathBuilder->getLogPath());
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     *
     * @return FileExceptionLoggerBuilder
     */
    public function setConfigurationConsulter(
        ConfigurationConsulter $configurationConsulter
    ): FileExceptionLoggerBuilder
    {
        $this->configurationConsulter = $configurationConsulter;

        return $this;
    }
}