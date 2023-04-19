<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheDataLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class AggregatedCacheDataLoader implements CacheDataLoaderInterface
{
    use CacheDataLoaderTrait
    {
        clearCacheData as protected clearAdapterCache;
    }

    /**
     * @var \Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface[]
     */
    private array $dataLoaders;

    /**
     * @param \Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface[] $dataLoaders
     */
    public function __construct(AdapterInterface $cacheAdapter, array $dataLoaders = [])
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->dataLoaders = $dataLoaders;
    }

    public function clearCacheData(): bool
    {
        foreach ($this->getDataLoaders() as $dataLoader)
        {
            if (!$dataLoader->clearCacheData())
            {
                return false;
            }
        }

        return $this->clearCacheData();
    }

    protected function getDataForCache(): array
    {
        $data = [];

        foreach ($this->getDataLoaders() as $dataLoader)
        {
            $data = array_merge_recursive($data, $dataLoader->loadCacheData());
        }

        return $data;
    }

    /**
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface[]
     */
    protected function getDataLoaders(): array
    {
        return $this->dataLoaders;
    }
}
