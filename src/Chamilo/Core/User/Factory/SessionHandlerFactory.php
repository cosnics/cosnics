<?php
namespace Chamilo\Core\User\Factory;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\User\Service\SessionHandler;
use Chamilo\Core\User\Storage\Repository\SessionRepository;

/**
 *
 * @package Chamilo\Core\User\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionHandlerFactory
{

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    private $fileConfigurationLocator;

    /**
     *
     * @var string
     */
    private $configuredSessionHandler;

    /**
     *
     * @var \Chamilo\Core\User\Storage\Repository\SessionRepository
     */
    private $sessionRepository;

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     * @param string $configuredSessionHandler
     * @param \Chamilo\Core\User\Storage\Repository\SessionRepository
     */
    public function __construct(FileConfigurationLocator $fileConfigurationLocator, $configuredSessionHandler,
        SessionRepository $sessionRepository = null)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->configuredSessionHandler = $configuredSessionHandler;
        $this->sessionRepository = $sessionRepository;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    public function getFileConfigurationLocator()
    {
        return $this->fileConfigurationLocator;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     */
    public function setFileConfigurationLocator(FileConfigurationLocator $fileConfigurationLocator)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
    }

    /**
     *
     * @return string
     */
    public function getConfiguredSessionHandler()
    {
        return $this->configuredSessionHandler;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\Repository\SessionRepository
     */
    public function getSessionRepository()
    {
        return $this->sessionRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\SessionRepository $sessionRepository
     */
    public function setSessionRepository(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    /**
     *
     * @param string $configuredSessionHandler
     */
    public function setConfiguredSessionHandler($configuredSessionHandler)
    {
        $this->configuredConfiguredSessionHandler = $configuredSessionHandler;
    }

    /**
     *
     * @return \Chamilo\Core\User\Service\SessionHandler|NULL
     */
    public function getSessionHandler()
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
}

