<?php
namespace Chamilo\Libraries\Cache\Traits;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait CacheAdapterHandlerTrait
{
    protected AdapterInterface $cacheAdapter;

    public function clearAllCacheData(): bool
    {
        return $this->getCacheAdapter()->clear();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForKey(string $cacheKey): bool
    {
        try
        {
            return $this->getCacheAdapter()->deleteItem($cacheKey);
        }
        catch (InvalidArgumentException $exception)
        {
            throw new CacheException('Could not clear cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForKeyParts(array $cacheKeyParts): bool
    {
        return $this->clearCacheDataForKey(
            $this->getCacheKeyForParts($cacheKeyParts)
        );
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
    }

    public function getCacheKeyForParts(array $cacheKeyParts): string
    {
        return md5(serialize($cacheKeyParts));
    }

    public function hasCacheDataForKey(string $cacheKey): bool
    {
        try
        {
            return $this->getCacheAdapter()->getItem($cacheKey)->isHit();
        }
        catch (InvalidArgumentException $exception)
        {
            return false;
        }
    }

    /**
     * @param string $cacheKey
     * @param callable $dataSource
     *
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheDataForKey(string $cacheKey, callable $dataSource)
    {
        try
        {
            if (!$this->hasCacheDataForKey($cacheKey))
            {
                $this->saveCacheDataForKey($cacheKey, call_user_func($dataSource));
            }

            return $this->readCacheDataForKey($cacheKey);
        }
        catch (CacheException $exception)
        {
            throw new CacheException('Could not load cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }

    /**
     * @param string[] $cacheKeyParts
     * @param callable $dataSource
     *
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheDataForKeyParts(array $cacheKeyParts, callable $dataSource)
    {
        return $this->loadCacheDataForKey(
            $this->getCacheKeyForParts($cacheKeyParts), $dataSource
        );
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForKey(string $cacheKey)
    {
        try
        {
            return $this->getCacheAdapter()->getItem($cacheKey)->get();
        }
        catch (InvalidArgumentException $exception)
        {
            throw new CacheException('Could not load cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForKeyParts(array $cacheKeyParts)
    {
        return $this->readCacheDataForKey($this->getCacheKeyForParts($cacheKeyParts));
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheDataForKey(string $cacheKey, callable $dataSource)
    {
        try
        {
            $this->clearCacheDataForKey($cacheKey);

            return $this->loadCacheDataForKey($cacheKey, $dataSource);
        }
        catch (CacheException $exception)
        {
            throw new CacheException('Could not reload cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }

    /**
     * @param string[] $cacheKeyParts
     *
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheDataForKeyParts(array $cacheKeyParts, callable $dataSource)
    {
        return $this->reloadCacheDataForKey($this->getCacheKeyForParts($cacheKeyParts), $dataSource);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheDataForKey(string $cacheKey, $cacheData, ?int $lifetime = null): bool
    {
        try
        {
            $cacheAdapter = $this->getCacheAdapter();
            $cacheItem = $cacheAdapter->getItem($cacheKey);
            // TODO: Make sure null being passed on here is not a problem
            $cacheItem->expiresAfter($lifetime);
            $cacheItem->set($cacheData);

            return $cacheAdapter->save($cacheItem);
        }
        catch (InvalidArgumentException $exception)
        {
            throw new CacheException('Could not save cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }
}