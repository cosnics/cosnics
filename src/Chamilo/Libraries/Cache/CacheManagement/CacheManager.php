<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

use Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;

/**
 * Cache director to clear and / or warmup caches
 *
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheManager
{

    /**
     * The cache services which have the possibility to be reset (clear and warmup)
     *
     * @var \Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface[]
     */
    protected $cacheServices;

    public function __construct()
    {
        $this->cacheServices = array();
    }

    /**
     * Adds a cache service to the list of cache warmers
     *
     * @param string $alias
     * @param \Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface $cacheService
     */
    public function addCacheService($alias, CacheResetterInterface $cacheService)
    {
        $this->cacheServices[$alias] = $cacheService;
    }

    /**
     * Returns a list of cache service aliases
     *
     * @return string[]
     */
    public function getCacheServiceAliases()
    {
        return array_keys($this->cacheServices);
    }

    /**
     * Returns the cache warmers
     *
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface[]
     */
    public function getCacheServices()
    {
        return $this->cacheServices;
    }

    /**
     * Clears the cache
     *
     * @param string[] $cacheServiceAliases
     */
    public function clear($cacheServiceAliases = array())
    {
        $cacheServices = $this->getCacheServicesByAliases($cacheServiceAliases);

        foreach ($cacheServices as $cacheService)
        {
            try
            {
                $cacheService->clear();
            }
            catch(\Exception $ex)
            {
                echo 'Something went wrong while clearing the cache for service ' . get_class($cacheService);
            }

        }
    }

    /**
     * Warm up the cache
     *
     * @param string[] $cacheServiceAliases
     */
    public function warmUp($cacheServiceAliases = array())
    {
        $cacheServices = $this->getCacheServicesByAliases($cacheServiceAliases);

        foreach ($cacheServices as $cacheService)
        {
            if (! $cacheService instanceof UserBasedCacheInterface)
            {
                try
                {
                    $cacheService->warmUp();
                }
                catch(\Exception $ex)
                {
                    echo 'Something went wrong while warming up the cache for service ' . get_class($cacheService);
                }
            }
        }
    }

    /**
     * Retrieves cache services by a given array of aliasses, throws an exception if an alias is used that does not
     * exist
     *
     * @param string[] $cacheServiceAliases
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheResetterInterface[]
     */
    protected function getCacheServicesByAliases($cacheServiceAliases = array())
    {
        if (empty($cacheServiceAliases))
        {
            return $this->cacheServices;
        }

        $cacheServices = array();

        foreach ($cacheServiceAliases as $cacheServiceAlias)
        {
            if (! array_key_exists($cacheServiceAlias, $this->cacheServices))
            {
                throw new \InvalidArgumentException(
                    sprintf('The given cache service alias %s does not exist', $cacheServiceAlias));
            }

            $cacheServices[$cacheServiceAlias] = $this->cacheServices[$cacheServiceAlias];
        }

        return $cacheServices;
    }
}
