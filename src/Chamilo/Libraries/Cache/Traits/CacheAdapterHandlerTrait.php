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

    public function clearCacheDataForKey(string $cacheKey): bool
    {
        try
        {
            return $this->getCacheAdapter()->deleteItem($cacheKey);
        }
        catch (InvalidArgumentException $e)
        {
            return false;
        }
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
        catch (InvalidArgumentException $e)
        {
            return false;
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForKey(string $cacheKey)
    {
        try
        {
            return $this->getCacheAdapter()->getItem($cacheKey)->get();
        }
        catch (InvalidArgumentException $e)
        {
            throw new CacheException('Could not load cache in ' . static::class . 'for key ' . $cacheKey);
        }
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
        catch (InvalidArgumentException $e)
        {
            throw new CacheException('Could not save cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }
}