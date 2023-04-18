<?php
namespace Chamilo\Libraries\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait CacheDataLoaderTrait
{
    use CacheDataSaverTrait
    {
        clearCacheData as clearCacheDataForKey;
        loadData as loadCacheDataForKey;
        saveCacheData as saveCacheDataForKey;
    }

    protected AdapterInterface $cacheAdapter;

    public function clearCacheData(): bool
    {
        return $this->clearCacheDataForKey($this->getCacheKey());
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
    }

    public function getCacheKey(): string
    {
        return md5(static::class);
    }

    abstract protected function getDataForCache();

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadData()
    {
        $cacheKey = $this->getCacheKey();

        if (!$this->hasCacheData($cacheKey))
        {
            $this->saveCacheDataForKey($cacheKey, $this->getDataForCache());
        }

        return $this->loadCacheDataForKey($cacheKey);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheData()
    {
        $this->clearCacheData();

        return $this->loadData();
    }

    public function saveCacheData(): bool
    {
        return $this->saveCacheDataForKey($this->getCacheKey(), $this->getDataForCache());
    }
}