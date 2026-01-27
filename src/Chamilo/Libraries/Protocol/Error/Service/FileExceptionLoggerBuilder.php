<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerBuilderInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Builds the FileExceptionLogger class
 *
 * @package Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{
    protected array $errorHandlingConfiguration;

    protected SessionInterface $session;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        SessionInterface $session, UrlGenerator $urlGenerator, array $errorHandlingConfiguration = []
    )
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->errorHandlingConfiguration = $errorHandlingConfiguration;
    }

    /**
     * @throws \Exception
     */
    public function createExceptionLogger(): FileExceptionLogger
    {
        $errorHandlingConfiguration = $this->getErrorHandlingConfiguration();

        return new FileExceptionLogger($errorHandlingConfiguration['logs_path']);
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