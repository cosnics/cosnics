<?php
namespace Chamilo\Libraries\Cache\Traits;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SimpleCacheDataPreLoaderTrait
{
    abstract public function loadCacheData();

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function preLoadCachedData()
    {
        return $this->loadCacheData();
    }
}