<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
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

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, SessionUtilities $sessionUtilities, UrlGenerator $urlGenerator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->sessionUtilities = $sessionUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Exception
     */
    public function createExceptionLogger(): FileExceptionLogger
    {

        $configurablePathBuilder = new ConfigurablePathBuilder(
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Configuration', 'storage'])
        );

        return new FileExceptionLogger($configurablePathBuilder->getLogPath());
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter
    ): FileExceptionLoggerBuilder
    {
        $this->configurationConsulter = $configurationConsulter;

        return $this;
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

}