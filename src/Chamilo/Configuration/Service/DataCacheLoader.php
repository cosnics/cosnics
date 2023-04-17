<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Chamilo\Configuration\Interfaces\DataLoaderInterface;
use Chamilo\Libraries\Cache\SymfonyCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class DataCacheLoader extends SymfonyCacheService implements DataLoaderInterface
{

    private CacheableDataLoaderInterface $cacheableDataLoader;

    public function __construct(
        AdapterInterface $cacheAdapter, ConfigurablePathBuilder $configurablePathBuilder,
        CacheableDataLoaderInterface $cacheableDataLoader
    )
    {
        parent::__construct($cacheAdapter, $configurablePathBuilder);

        $this->cacheableDataLoader = $cacheableDataLoader;
    }

    public function clearData()
    {
        $this->getCacheableDataLoader()->clearData();

        return $this->getCacheAdapter()->delete($this->getCacheableDataLoader()->getIdentifier());
    }

    protected function getCacheableDataLoader(): CacheableDataLoaderInterface
    {
        return $this->cacheableDataLoader;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getData()
    {
        return $this->getForIdentifier($this->getCacheableDataLoader()->getIdentifier());
    }

    public function getIdentifiers(): array
    {
        return [$this->getCacheableDataLoader()->getIdentifier()];
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function loadData(): bool
    {
        $cacheItem = $this->getCacheAdapter()->getItem($this->getCacheableDataLoader()->getIdentifier());
        $cacheItem->set($this->getCacheableDataLoader()->getData());

        return $this->getCacheAdapter()->save($cacheItem);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUpForIdentifier($identifier): bool
    {
        return $this->loadData();
    }
}