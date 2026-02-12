<?php
namespace Chamilo\Libraries\DependencyInjection\Service;

use Chamilo\Libraries\Storage\Service\FileBasedCacheService;

/**
 * @package Chamilo\Libraries\DependencyInjection\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionCacheService extends FileBasedCacheService
{
    public function getCachePath(): string
    {
        return $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__);
    }

    /**
     * @throws \Exception
     */
    public function initializeCache(): void
    {
        $dependencyInjectionContainerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $dependencyInjectionContainerBuilder->clearContainerInstance();
        $dependencyInjectionContainerBuilder->createContainer();
    }
}