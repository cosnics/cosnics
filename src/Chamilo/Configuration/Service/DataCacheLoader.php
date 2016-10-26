<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Chamilo\Configuration\Interfaces\DataLoaderInterface;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataCacheLoader extends DoctrinePhpFileCacheService implements DataLoaderInterface
{

    /**
     *
     * @var \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface
     */
    private $cacheableDataLoader;

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface $cacheableDataLoader
     */
    public function __construct(CacheableDataLoaderInterface $cacheableDataLoader)
    {
        $this->cacheableDataLoader = $cacheableDataLoader;
    }

    /**
     *
     * @return \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface
     */
    protected function getCacheableDataLoader()
    {
        return $this->cacheableDataLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface $cacheableDataLoader
     */
    protected function setCacheableDataLoader(CacheableDataLoaderInterface $cacheableDataLoader)
    {
        $this->cacheableDataLoader = $cacheableDataLoader;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        return $this->loadSettings();
    }

    /**
     *
     * @return boolean
     */
    public function loadSettings()
    {
        return $this->getCacheProvider()->save(
            $this->getCacheableDataLoader()->getIdentifier(),
            $this->getCacheableDataLoader()->getData());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Configuration';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array($this->getCacheableDataLoader()->getIdentifier());
    }

    /**
     *
     * @return string[]
     */
    public function getData()
    {
        return $this->getForIdentifier($this->getCacheableDataLoader()->getIdentifier());
    }
}