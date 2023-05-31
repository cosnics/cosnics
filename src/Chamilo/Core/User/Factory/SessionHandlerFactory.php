<?php
namespace Chamilo\Core\User\Factory;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\User\Service\SessionHandler;
use Chamilo\Core\User\Storage\Repository\SessionRepository;
use SessionHandlerInterface;

/**
 * @package Chamilo\Core\User\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SessionHandlerFactory
{

    private string $configuredSessionHandler;

    private FileConfigurationLocator $fileConfigurationLocator;

    private ?SessionRepository $sessionRepository;

    public function __construct(
        FileConfigurationLocator $fileConfigurationLocator, string $configuredSessionHandler,
        ?SessionRepository $sessionRepository = null
    )
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->configuredSessionHandler = $configuredSessionHandler;
        $this->sessionRepository = $sessionRepository;
    }

    public function getConfiguredSessionHandler(): string
    {
        return $this->configuredSessionHandler;
    }

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }

    public function getSessionHandler(): ?SessionHandlerInterface
    {
        if ($this->getFileConfigurationLocator()->isAvailable())
        {
            if ($this->getConfiguredSessionHandler() == 'chamilo')
            {
                return new SessionHandler($this->getSessionRepository());
            }
        }

        return null;
    }

    public function getSessionRepository(): SessionRepository
    {
        return $this->sessionRepository;
    }
}

