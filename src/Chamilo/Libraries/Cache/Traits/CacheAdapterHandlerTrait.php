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

    public function clearAllCacheDataForAdapter(AdapterInterface $cacheAdapter): bool
    {
        return $cacheAdapter->clear();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForAdapterAndKey(AdapterInterface $cacheAdapter, string $cacheKey): bool
    {
        try
        {
            return $cacheAdapter->deleteItem($cacheKey);
        }
        catch (InvalidArgumentException)
        {
            throw new CacheException('Could not clear cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForAdapterAndKeyParts(AdapterInterface $cacheAdapter, array $cacheKeyParts): bool
    {
        return $this->clearCacheDataForAdapterAndKey(
            $cacheAdapter, $this->getCacheKeyForParts($cacheKeyParts)
        );
    }

    public function getCacheKeyForParts(array $cacheKeyParts): string
    {
        return md5(serialize($cacheKeyParts));
    }

    public function hasCacheDataForAdapterAndKey(AdapterInterface $cacheAdapter, string $cacheKey): bool
    {
        try
        {
            return $cacheAdapter->getItem($cacheKey)->isHit();
        }
        catch (InvalidArgumentException)
        {
            return false;
        }
    }

    /**
     * @param string[] $cacheKeyParts
     */
    public function hasCacheDataForAdapterAndKeyParts(AdapterInterface $cacheAdapter, array $cacheKeyParts): bool
    {
        return $this->hasCacheDataForAdapterAndKey($cacheAdapter, $this->getCacheKeyForParts($cacheKeyParts));
    }

    /**
     * @param string $cacheKey
     * @param callable $dataSource
     *
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheDataForAdapterAndKey(AdapterInterface $cacheAdapter, string $cacheKey, callable $dataSource
    )
    {
        try
        {
            if (!$this->hasCacheDataForAdapterAndKey($cacheAdapter, $cacheKey))
            {
                $this->saveCacheDataForAdapterAndKey($cacheAdapter, $cacheKey, call_user_func($dataSource));
            }

            return $this->readCacheDataForAdapterAndKey($cacheAdapter, $cacheKey);
        }
        catch (CacheException)
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
    public function loadCacheDataForAdapterAndKeyParts(
        AdapterInterface $cacheAdapter, array $cacheKeyParts, callable $dataSource
    )
    {
        return $this->loadCacheDataForAdapterAndKey(
            $cacheAdapter, $this->getCacheKeyForParts($cacheKeyParts), $dataSource
        );
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForAdapterAndKey(AdapterInterface $cacheAdapter, string $cacheKey)
    {
        try
        {
            return $cacheAdapter->getItem($cacheKey)->get();
        }
        catch (InvalidArgumentException)
        {
            throw new CacheException('Could not load cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForAdapterAndKeyParts(AdapterInterface $cacheAdapter, array $cacheKeyParts)
    {
        return $this->readCacheDataForAdapterAndKey($cacheAdapter, $this->getCacheKeyForParts($cacheKeyParts));
    }

    /**
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheDataForAdapterAndKey(
        AdapterInterface $cacheAdapter, string $cacheKey, callable $dataSource
    )
    {
        try
        {
            $this->clearCacheDataForAdapterAndKey($cacheAdapter, $cacheKey);

            return $this->loadCacheDataForAdapterAndKey($cacheAdapter, $cacheKey, $dataSource);
        }
        catch (CacheException)
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
    public function reloadCacheDataForAdapterAndKeyParts(
        AdapterInterface $cacheAdapter, array $cacheKeyParts, callable $dataSource
    )
    {
        return $this->reloadCacheDataForAdapterAndKey(
            $cacheAdapter, $this->getCacheKeyForParts($cacheKeyParts), $dataSource
        );
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheDataForAdapterAndKey(
        AdapterInterface $cacheAdapter, string $cacheKey, $cacheData, ?int $lifetime = null
    ): bool
    {
        try
        {
            $cacheItem = $cacheAdapter->getItem($cacheKey);
            // TODO: Make sure null being passed on here is not a problem
            $cacheItem->expiresAfter($lifetime);
            $cacheItem->set($cacheData);

            return $cacheAdapter->save($cacheItem);
        }
        catch (InvalidArgumentException)
        {
            throw new CacheException('Could not save cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheDataForAdapterAndKeyParts(
        AdapterInterface $cacheAdapter, array $cacheKeyParts, $cacheData, ?int $lifetime = null
    ): bool
    {
        return $this->saveCacheDataForAdapterAndKey(
            $cacheAdapter, $this->getCacheKeyForParts($cacheKeyParts), $cacheData, $lifetime
        );
    }
}