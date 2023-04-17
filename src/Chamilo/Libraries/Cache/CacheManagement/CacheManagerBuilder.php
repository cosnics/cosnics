<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

/**
 * Builds the cache manager by adding custom cache services directly (through code) and indirectly
 * (through dependency injection).
 * Uses CacheServiceConstructor classes so multiple packages can provide
 * their own cache services
 *
 * @package Chamilo\Libraries\Cache\CacheManagement
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CacheManagerBuilder
{

    /**
     * @var \Chamilo\Libraries\Cache\CacheManagement\CacheServicesConstructorInterface[]
     */
    protected $cacheServicesConstructors;

    public function __construct()
    {
        $this->cacheServicesConstructors = [];
    }

    /**
     * @param \Chamilo\Libraries\Cache\CacheManagement\CacheServicesConstructorInterface $cacheServicesConstructor
     */
    public function addCacheServiceConstructor(CacheServicesConstructorInterface $cacheServicesConstructor)
    {
        $this->cacheServicesConstructors[] = $cacheServicesConstructor;
    }

    /**
     * Builds the cache director and adds the chamilo cache services through code
     *
     * @return \Chamilo\Libraries\Cache\CacheManagement\CacheManager
     */
    public function buildCacheManager()
    {
        $cacheManager = new CacheManager();

        foreach ($this->cacheServicesConstructors as $cacheServicesConstructor)
        {
            $cacheServicesConstructor->createCacheServices($cacheManager);
        }

        return $cacheManager;
    }
}