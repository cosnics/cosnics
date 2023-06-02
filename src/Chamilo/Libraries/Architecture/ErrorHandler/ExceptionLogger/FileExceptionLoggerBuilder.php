<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Builds the FileExceptionLogger class
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{
    protected ConfigurationConsulter $configurationConsulter;

    protected SessionInterface $session;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, SessionInterface $session, UrlGenerator $urlGenerator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->session = $session;
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

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter
    ): FileExceptionLoggerBuilder
    {
        $this->configurationConsulter = $configurationConsulter;

        return $this;
    }

}