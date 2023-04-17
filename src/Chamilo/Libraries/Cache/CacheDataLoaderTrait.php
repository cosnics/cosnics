<?php
namespace Chamilo\Libraries\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait CacheDataLoaderTrait
{
    protected AdapterInterface $cacheAdapter;

    public function clearCache(): bool
    {
        return $this->getCacheAdapter()->clear();
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
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function loadCache(): bool
    {
        $cacheAdapter = $this->getCacheAdapter();
        $cacheItem = $cacheAdapter->getItem($this->getCacheKey());

        if (!$cacheItem->isHit())
        {
            $cacheItem->set($this->getDataForCache());

            return $cacheAdapter->save($cacheItem);
        }

        return true;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function readData()
    {
        $cacheAdapter = $this->getCacheAdapter();
        $cacheItem = $cacheAdapter->getItem($this->getCacheKey());

        if (!$cacheItem->isHit())
        {
            $this->loadCache();
            $cacheItem = $cacheAdapter->getItem($this->getCacheKey());
        }

        return $cacheItem->get();
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function reloadCache(): bool
    {
        if ($this->clearCache())
        {
            return $this->loadCache();
        }

        throw new CacheException('Could not clear cache');
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function rereadData()
    {
        if ($this->clearCache())
        {
            return $this->readData();
        }

        throw new CacheException('Could not clear cache');
    }
}