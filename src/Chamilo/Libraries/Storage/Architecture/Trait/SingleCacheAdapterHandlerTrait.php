<?php
namespace Chamilo\Libraries\Storage\Architecture\Trait;

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Libraries\Cache\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait SingleCacheAdapterHandlerTrait
{
    use CacheAdapterHandlerTrait;

    protected AdapterInterface $cacheAdapter;

    public function clearAllCacheData(): bool
    {
        return $this->clearAllCacheDataForAdapter($this->getCacheAdapter());
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForKey(string $cacheKey): bool
    {
        return $this->clearCacheDataForAdapterAndKey($this->getCacheAdapter(), $cacheKey);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function clearCacheDataForKeyParts(array $cacheKeyParts): bool
    {
        return $this->clearCacheDataForAdapterAndKeyParts($this->getCacheAdapter(), $cacheKeyParts);
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
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
        return $this->hasCacheDataForAdapterAndKey($this->getCacheAdapter(), $cacheKey);
    }

    /**
     * @param string[] $cacheKeyParts
     */
    public function hasCacheDataForKeyParts(array $cacheKeyParts): bool
    {
        return $this->hasCacheDataForAdapterAndKeyParts($this->getCacheAdapter(), $cacheKeyParts);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheDataForKey(string $cacheKey, callable $dataSource): mixed
    {
        return $this->loadCacheDataForAdapterAndKey($this->getCacheAdapter(), $cacheKey, $dataSource);
    }

    /**
     * @param string[] $cacheKeyParts
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function loadCacheDataForKeyParts(array $cacheKeyParts, callable $dataSource): mixed
    {
        return $this->loadCacheDataForAdapterAndKeyParts($this->getCacheAdapter(), $cacheKeyParts, $dataSource);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForKey(string $cacheKey): mixed
    {
        return $this->readCacheDataForAdapterAndKey($this->getCacheAdapter(), $cacheKey);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function readCacheDataForKeyParts(array $cacheKeyParts): mixed
    {
        return $this->readCacheDataForAdapterAndKeyParts($this->getCacheAdapter(), $cacheKeyParts);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheDataForKey(string $cacheKey, callable $dataSource): mixed
    {
        return $this->reloadCacheDataForAdapterAndKey($this->getCacheAdapter(), $cacheKey, $dataSource);
    }

    /**
     * @param string[] $cacheKeyParts
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCacheDataForKeyParts(array $cacheKeyParts, callable $dataSource): mixed
    {
        return $this->reloadCacheDataForAdapterAndKeyParts($this->getCacheAdapter(), $cacheKeyParts, $dataSource);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheDataForKey(string $cacheKey, $cacheData, ?int $lifetime = null): bool
    {
        return $this->saveCacheDataForAdapterAndKey($this->getCacheAdapter(), $cacheKey, $cacheData, $lifetime);
    }

    /**
     * @param string[] $cacheKeyParts
     *
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function saveCacheDataForKeyParts(array $cacheKeyParts, $cacheData, ?int $lifetime = null): bool
    {
        return $this->saveCacheDataForAdapterAndKeyParts(
            $this->getCacheAdapter(), $cacheKeyParts, $cacheData, $lifetime
        );
    }
}