<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerBuilderInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
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

    protected array $errorHandlingConfiguration;

    protected SessionInterface $session;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        SessionInterface $session, UrlGenerator $urlGenerator, array $errorHandlingConfiguration = []
    )
    {
        $this->errorHandlingConfiguration = $errorHandlingConfiguration;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Exception
     */
    public function createExceptionLogger(): SentryExceptionLogger
    {
        $errorHandlingConfiguration = $this->getErrorHandlingConfiguration();

        $clientDSNKey = $errorHandlingConfiguration['DSN'];

        if (empty($clientDSNKey))
        {
            throw new Exception(
                'The DSN key should be configured when using the sentry exception logger. ' .
                'The configuration should be put in ' .
                'chamilo.configuration.error_handling["configuration"]["Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\SentryExceptionLoggerBuilder"]["DSN"]'
            );
        }

        return new SentryExceptionLogger($this->getSession(), $this->getUrlGenerator(), $clientDSNKey);
    }

    public function getErrorHandlingConfiguration(): array
    {
        return $this->errorHandlingConfiguration;
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