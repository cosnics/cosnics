<?php
namespace Chamilo\Libraries\Protocol\ErrorHandling\Service;

use Chamilo\Libraries\Protocol\ErrorHandling\Architecture\Interface\ExceptionLoggerBuilderInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\Protocol\ErrorHandling\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class FileExceptionLoggerBuilder implements ExceptionLoggerBuilderInterface
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
    public function createExceptionLogger(): FileExceptionLogger
    {
        return new FileExceptionLogger(
            $this->userExceptionRendererRegistry, $this->errorHandlingConfiguration['logsPath']
        );
    }
}