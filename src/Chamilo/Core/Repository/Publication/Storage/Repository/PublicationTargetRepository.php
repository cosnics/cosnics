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