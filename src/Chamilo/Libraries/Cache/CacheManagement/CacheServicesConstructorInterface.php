<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

/**
 * Interface for services to construct the cache services for the CacheManager
 *
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
interface CacheServicesConstructorInterface
{

    /**
     * Creates and adds the cache services to the given cache manager
     *
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheManager $cacheManager
     */
    public function createCacheServices(CacheManager $cacheManager);
}