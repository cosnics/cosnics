<?php
namespace Chamilo\Libraries\Storage\Architecture\Trait;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Trait
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SingleCacheAdapterHandlerTrait
{
    use CacheAdapterHandlerTrait;

    protected readonly AdapterInterface $cacheAdapter;

    public function clearAllCacheData(): bool
    {
        return $this->clearAllCacheDataForAdapter($this->cacheAdapter);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForKey(string $cacheKey): bool
    {
        return $this->clearCacheDataForAdapterAndKey($this->cacheAdapter, $cacheKey);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForKeyParts(array $cacheKeyParts): bool
    {
        return $this->clearCacheDataForAdapterAndKeyParts($this->cacheAdapter, $cacheKeyParts);
    }

    /**
     * @param string[] $cacheKeyParts
     */
    public function getCacheKeyForParts(array $cacheKeyParts): string
    {
        return md5(serialize($cacheKeyParts));
    }

    public function hasCacheDataForKey(string $cacheKey): bool
    {
        return $this->hasCacheDataForAdapterAndKey($this->cacheAdapter, $cacheKey);
    }

    /**
     * @param string[] $cacheKeyParts
     */
    public function hasCacheDataForKeyParts(array $cacheKeyParts): bool
    {
        return $this->hasCacheDataForAdapterAndKeyParts($this->cacheAdapter, $cacheKeyParts);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheDataForKey(string $cacheKey, callable $dataSource): mixed
    {
        return $this->loadCacheDataForAdapterAndKey($this->cacheAdapter, $cacheKey, $dataSource);
    }

    /**
     * @param string[] $cacheKeyParts
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheDataForKeyParts(array $cacheKeyParts, callable $dataSource): mixed
    {
        return $this->loadCacheDataForAdapterAndKeyParts($this->cacheAdapter, $cacheKeyParts, $dataSource);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForKey(string $cacheKey): mixed
    {
        return $this->readCacheDataForAdapterAndKey($this->cacheAdapter, $cacheKey);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForKeyParts(array $cacheKeyParts): mixed
    {
        return $this->readCacheDataForAdapterAndKeyParts($this->cacheAdapter, $cacheKeyParts);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheDataForKey(string $cacheKey, callable $dataSource): mixed
    {
        return $this->reloadCacheDataForAdapterAndKey($this->cacheAdapter, $cacheKey, $dataSource);
    }

    /**
     * @param string[] $cacheKeyParts
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheDataForKeyParts(array $cacheKeyParts, callable $dataSource): mixed
    {
        return $this->reloadCacheDataForAdapterAndKeyParts($this->cacheAdapter, $cacheKeyParts, $dataSource);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheDataForKey(string $cacheKey, $cacheData, ?int $lifetime = null): bool
    {
        return $this->saveCacheDataForAdapterAndKey($this->cacheAdapter, $cacheKey, $cacheData, $lifetime);
    }

    /**
     * @param string[] $cacheKeyParts
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheDataForKeyParts(array $cacheKeyParts, $cacheData, ?int $lifetime = null): bool
    {
        return $this->saveCacheDataForAdapterAndKeyParts(
            $this->cacheAdapter, $cacheKeyParts, $cacheData, $lifetime
        );
    }
}