<?php
namespace Chamilo\Libraries\Cache\Traits;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SimpleCacheAdapterHandlerTrait
{
    use SingleCacheAdapterHandlerTrait;

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheData(): bool
    {
        return $this->clearCacheDataForKey($this->getCacheKey());
    }

    public function getCacheKey(): string
    {
        return $this->getCacheKeyForParts([static::class]);
    }

    abstract protected function getDataForCache();

    public function hasCacheData(): bool
    {
        return $this->hasCacheDataForKey($this->getCacheKey());
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheData()
    {
        return $this->loadCacheDataForKey($this->getCacheKey(), [$this, 'getDataForCache']);
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheData()
    {
        return $this->readCacheDataForKey($this->getCacheKey());
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheData()
    {
        return $this->reloadCacheDataForKey($this->getCacheKey(), [$this, 'getDataForCache']);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheData(): bool
    {
        return $this->saveCacheDataForKey($this->getCacheKey(), call_user_func([$this, 'getDataForCache']));
    }
}