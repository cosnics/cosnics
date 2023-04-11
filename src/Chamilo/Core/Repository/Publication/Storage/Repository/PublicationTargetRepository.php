<?php
namespace Chamilo\Core\Repository\Publication\Storage\Repository;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Core\Repository\Publication\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRepository
{
    private AdapterInterface $cacheAdapter;

    public function __construct(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addModifierServiceIdentifier(string $key, string $modifierServiceIdentifier): bool
    {
        $cacheAdapter = $this->getCacheAdapter();

        $cacheItem = $cacheAdapter->getItem($key);
        $cacheItem->set($modifierServiceIdentifier);

        return $cacheAdapter->save($cacheItem);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addPublicationTarget(string $key, PublicationTarget $publicationTarget): bool
    {
        $cacheAdapter = $this->getCacheAdapter();

        $cacheItem = $cacheAdapter->getItem($key);
        $cacheItem->set($publicationTarget);

        return $cacheAdapter->save($cacheItem);
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getModifierServiceIdentifier(string $key): string
    {
        $cacheAdapter = $this->getCacheAdapter();

        $cacheItem = $cacheAdapter->getItem($key);
        if (!$cacheItem->isHit())
        {
            throw new CacheException();
        }

        return $cacheItem->get();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPublicationTarget(string $key): PublicationTarget
    {
        $cacheAdapter = $this->getCacheAdapter();

        $cacheItem = $cacheAdapter->getItem($key);
        if (!$cacheItem->isHit())
        {
            throw new CacheException();
        }

        return $cacheItem->get();
    }

    public function setCacheAdapter(AdapterInterface $cacheAdapter): void
    {
        $this->cacheAdapter = $cacheAdapter;
    }
}