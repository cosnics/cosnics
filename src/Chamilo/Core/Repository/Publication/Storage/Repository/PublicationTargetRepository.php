<?php
namespace Chamilo\Core\Repository\Publication\Storage\Repository;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Doctrine\Common\Cache\CacheProvider;

/**
 * @package Chamilo\Core\Repository\Publication\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRepository
{
    /**
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    private $cacheProvider;

    /**
     * @param \Doctrine\Common\Cache\CacheProvider $cacheProvider
     */
    public function __construct(CacheProvider $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param string $key
     * @param string $modifierServiceIdentifier
     *
     * @return bool
     */
    public function addModifierServiceIdentifier(string $key, string $modifierServiceIdentifier)
    {
        return $this->getCacheProvider()->save($key, $modifierServiceIdentifier);
    }

    /**
     * @param string $key
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     *
     * @return bool
     */
    public function addPublicationTarget(string $key, PublicationTarget $publicationTarget)
    {
        return $this->getCacheProvider()->save($key, $publicationTarget);
    }

    /**
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getCacheProvider(): CacheProvider
    {
        return $this->cacheProvider;
    }

    /**
     * @param \Doctrine\Common\Cache\CacheProvider $cacheProvider
     */
    public function setCacheProvider(CacheProvider $cacheProvider): void
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getModifierServiceIdentifier(string $key)
    {
        return $this->getCacheProvider()->fetch($key);
    }

    /**
     * @param string $key
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
     */
    public function getPublicationTarget(string $key)
    {
        return $this->getCacheProvider()->fetch($key);
    }
}