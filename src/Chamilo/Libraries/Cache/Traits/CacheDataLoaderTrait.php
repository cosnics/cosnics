<?php
namespace Chamilo\Libraries\Cache\Traits;

use Symfony\Component\Cache\Exception\CacheException;
use Throwable;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait CacheDataLoaderTrait
{
    use CacheAdapterHandlerTrait;

    public function clearCacheData(): bool
    {
        return $this->clearCacheDataForKey($this->getCacheKey());
    }

    public function getCacheKey(): string
    {
        return $this->getCacheKeyForParts([static::class]);
    }

    abstract protected function getDataForCache();

    public function loadCacheData(): bool
    {
        $cacheKey = $this->getCacheKey();

        if (!$this->hasCacheDataForKey($cacheKey))
        {
            try
            {
                return $this->saveCacheData();
            }
            catch (CacheException $e)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheData()
    {
        if (!$this->loadCacheData())
        {
            throw new CacheException('Could not load data for cache in ' . static::class);
        }

        try
        {
            return $this->readCacheDataForKey($this->getCacheKey());
        }
        catch (CacheException $e)
        {
            throw new CacheException('Could not read data for cache in ' . static::class);
        }
    }

    public function reloadCacheData(): bool
    {
        if ($this->clearCacheData())
        {
            return $this->loadCacheData();
        }

        return false;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function rereadCacheData()
    {
        if (!$this->clearCacheData())
        {
            throw new CacheException('Could not clear data for cache in ' . static::class);
        }

        try
        {
            return $this->readCacheData();
        }
        catch (CacheException $e)
        {
            throw new CacheException('Could not read data for cache in ' . static::class);
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
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