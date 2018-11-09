<?php
namespace Chamilo\Core\Repository\Publication\Storage\Repository;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Psr\SimpleCache\CacheInterface;

/**
 * @package Chamilo\Core\Repository\Publication\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRepository
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cacheProvider;

    /**
     * @param \Psr\SimpleCache\CacheInterface $cacheProvider
     */
    public function __construct(CacheInterface $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param string $key
     * @param string $modifierServiceIdentifier
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addModifierServiceIdentifier(string $key, string $modifierServiceIdentifier)
    {
        return $this->getCacheProvider()->set($key, $modifierServiceIdentifier);
    }

    /**
     * @param string $key
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addPublicationTarget(string $key, PublicationTarget $publicationTarget)
    {
        return $this->getCacheProvider()->set($key, $publicationTarget);
    }

    /**
     * @return \Psr\SimpleCache\CacheInterface
     */
    public function getCacheProvider(): CacheInterface
    {
        return $this->cacheProvider;
    }

    /**
     * @param \Psr\SimpleCache\CacheInterface $cacheProvider
     */
    public function setCacheProvider(CacheInterface $cacheProvider): void
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param string $key
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getModifierServiceIdentifier(string $key)
    {
        return $this->getCacheProvider()->get($key);
    }

    /**
     * @param string $key
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getPublicationTarget(string $key)
    {
        return $this->getCacheProvider()->get($key);
    }
}