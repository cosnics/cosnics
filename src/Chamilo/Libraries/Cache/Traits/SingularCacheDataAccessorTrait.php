<?php
namespace Chamilo\Libraries\Cache\Traits;

use Symfony\Component\Cache\Exception\CacheException;
use Throwable;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SingularCacheDataAccessorTrait
{
    use CacheDataAccessorTrait
    {
        clearCacheData as clearCacheDataForKey;
        loadData as loadCacheDataForKey;
        saveCacheData as saveCacheDataForKey;
    }

    public function clearCacheData(): bool
    {
        return $this->clearCacheDataForKey($this->getCacheKey());
    }

    public function getCacheKey(): string
    {
        return $this->getCacheKeyForParts([static::class]);
    }

    abstract protected function getDataForCache();

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function loadData()
    {
        $cacheKey = $this->getCacheKey();

        if (!$this->hasCacheData($cacheKey))
        {
            $this->saveCacheData();
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
        try
        {
            return $this->saveCacheDataForKey($this->getCacheKey(), $this->getDataForCache());
        }
        catch (Throwable $throwable)
        {
            throw new CacheException('Could not get data for cache in ' . static::class);
        }
    }
}