<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use SessionHandlerInterface;

/**
 * @package Chamilo\Libraries\Platform\Session
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SessionHandlerFactory
{

    private string $configuredSessionHandler;

    private FileConfigurationLocator $fileConfigurationLocator;

    public function __construct(FileConfigurationLocator $fileConfigurationLocator, string $configuredSessionHandler)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->configuredSessionHandler = $configuredSessionHandler;
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
            return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                $this->getConfiguredSessionHandler()
            );
        }

        return null;
    }
}

