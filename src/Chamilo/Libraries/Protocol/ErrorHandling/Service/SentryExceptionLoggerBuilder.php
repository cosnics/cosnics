<?php
namespace Chamilo\Libraries\Protocol\ErrorHandling\Service;

use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerBuilderInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Protocol\ErrorHandling\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class SentryExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{
    public function __construct(
        protected SessionInterface $session, protected UrlGenerator $urlGenerator,
        protected UserExceptionRendererRegistry $userExceptionRendererRegistry,
        protected array $errorHandlingConfiguration = []
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function createExceptionLogger(): SentryExceptionLogger
    {
        $clientDSNKey = $this->errorHandlingConfiguration['dsn'];

        if (empty($clientDSNKey)) {
            throw new Exception(
                'The DSN key should be configured when using the sentry exception logger. ' .
                'The configuration should be put in ' .
                'chamilo.configuration.errorHandling["configuration"]["Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\SentryExceptionLoggerBuilder"]["dsn"]'
            );
        }

        return new SentryExceptionLogger($this->session, $this->urlGenerator, $clientDSNKey);
    }
}