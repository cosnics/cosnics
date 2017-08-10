<?php
namespace Chamilo\Libraries\Cache\CacheManagement;

/**
 * Builds the cache manager by adding custom cache services directly (through code) and indirectly
 * (through dependency injection). Uses CacheServiceConstructor classes so multiple packages can provide
 * their own cache services
 *
 * @package Chamilo\Libraries\Cache
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CacheManagerBuilder
{
    /**
     * @var CacheServicesConstructorInterface[]
     */
    protected $cacheServicesConstructors;

    /**
     * CacheManagerBuilder constructor.
     */
    public function __construct()
    {
        $this->cacheServicesConstructors = [];
    }

    /**
     * @param CacheServicesConstructorInterface $cacheServicesConstructor
     */
    public function addCacheServiceConstructor(CacheServicesConstructorInterface $cacheServicesConstructor)
    {
        $this->cacheServicesConstructors[] = $cacheServicesConstructor;
    }

    /**
     * Builds the cache director and adds the chamilo cache services through code
     *
     * @return CacheManager
     */
    public function buildCacheManager()
    {
        $cacheManager = new CacheManager();

        foreach($this->cacheServicesConstructors as $cacheServicesConstructor)
        {
            $cacheServicesConstructor->createCacheServices($cacheManager);
        }

        return $cacheManager;
    }
}