<?php
namespace Chamilo\Core\Repository\Publication\Storage\Repository;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Core\Repository\Publication\Storage\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRepository
{
    use CacheAdapterHandlerTrait;

    public function __construct(AdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function addModifierServiceIdentifier(string $key, string $modifierServiceIdentifier): bool
    {
        return $this->saveCacheDataForKey($key, $modifierServiceIdentifier);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function addPublicationTarget(string $key, PublicationTarget $publicationTarget): bool
    {
        return $this->saveCacheDataForKey($key, $publicationTarget);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getModifierServiceIdentifier(string $key): string
    {
        if (!$this->hasCacheDataForKey($key))
        {
            throw new CacheException();
        }

        return $this->readCacheDataForKey($key);
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getPublicationTarget(string $key): PublicationTarget
    {
        if (!$this->hasCacheDataForKey($key))
        {
            throw new CacheException();
        }

        return $this->readCacheDataForKey($key);
    }
}