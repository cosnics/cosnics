<?php
namespace Chamilo\Libraries\Cache\Traits;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait CacheDataAccessorTrait
{
    protected AdapterInterface $cacheAdapter;

    public function clearCacheData(string $cacheKey): bool
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

    public function hasCacheData(string $cacheKey): bool
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
    public function loadData(string $cacheKey)
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
    public function saveCacheData(string $cacheKey, $cacheData): bool
    {
        try
        {
            $cacheAdapter = $this->getCacheAdapter();
            $cacheItem = $cacheAdapter->getItem($cacheKey);
            $cacheItem->set($cacheData);

            return $cacheAdapter->save($cacheItem);
        }
        catch (InvalidArgumentException $e)
        {
            throw new CacheException('Could not save cache in ' . static::class . 'for key ' . $cacheKey);
        }
    }
}