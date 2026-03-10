<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Protocol\Error\Architecture\Interface\ExceptionLoggerBuilderInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
{
    protected array $errorHandlingConfiguration;

    protected SessionInterface $session;

    protected UrlGenerator $urlGenerator;

    protected UserExceptionRendererRegistry $userExceptionRendererRegistry;

    public function __construct(
        SessionInterface $session, UrlGenerator $urlGenerator,
        UserExceptionRendererRegistry $userExceptionRendererRegistry, array $errorHandlingConfiguration = []
    )
    {
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->errorHandlingConfiguration = $errorHandlingConfiguration;
        $this->userExceptionRendererRegistry = $userExceptionRendererRegistry;
    }

    /**
     * @throws \Exception
     */
    public function createExceptionLogger(): FileExceptionLogger
    {
        $errorHandlingConfiguration = $this->getErrorHandlingConfiguration();

        return new FileExceptionLogger(
            $this->getUserExceptionRendererRegistry(), $errorHandlingConfiguration['logsPath']
        );
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

    public function getUserExceptionRendererRegistry(): UserExceptionRendererRegistry
    {
        return $this->userExceptionRendererRegistry;
    }
}