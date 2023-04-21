<?php
namespace Chamilo\Libraries\Cache\Traits;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SimpleCacheDataLoaderTrait
{
    abstract public function loadCacheData();

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCachedData()
    {
        return $this->loadCacheData();
    }
}