<?php
namespace Chamilo\Libraries\DependencyInjection;

use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\Path;

/**
 * Manages the cache for the symfony dependency injection
 *
 * @package Chamilo\Libraries\DependencyInjection
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionCacheService extends FileBasedCacheService
{

    /**
     * Warms up the cache.
     */
    public function warmUp()
    {
        $dependencyInjectionContainerBuilder = new DependencyInjectionContainerBuilder();
        $dependencyInjectionContainerBuilder->clearContainerInstance();

        $dependencyInjectionContainerBuilder->createContainer();

        return $this;
    }

    /**
     * Returns the path to the cache directory or file
     *
     * @return string
     */
    function getCachePath()
    {
        return Path::getInstance()->getCachePath(__NAMESPACE__);
    }
}