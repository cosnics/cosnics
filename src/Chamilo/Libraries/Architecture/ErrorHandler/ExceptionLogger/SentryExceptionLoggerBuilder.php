<?php
namespace Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Builds the SentryExceptionLogger class
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
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
    public function createExceptionLogger(): SentryExceptionLogger
    {
        $clientDSNKey = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Configuration', 'error_handling', 'sentry_error_logger', 'DSN']
        );

        if (empty($clientDSNKey))
        {
            throw new Exception(
                'The DSN key should be configured when using the sentry exception logger. ' .
                'The configuration should be put in ' .
                'chamilo.configuration.error_handling["sentry_error_logger"]["DSN"]'
            );
        }

        return new SentryExceptionLogger($this->getSession(), $this->getUrlGenerator(), $clientDSNKey);
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

}