<?php
namespace Chamilo\Libraries\DependencyInjection\Service;

use Chamilo\Libraries\Storage\Service\FileBasedCacheService;

/**
 * Manages the cache for the symfony dependency injection
 *
 * @package Chamilo\Libraries\DependencyInjection
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