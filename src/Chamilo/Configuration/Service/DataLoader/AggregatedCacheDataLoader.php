<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Libraries\Cache\CacheDataLoaderTrait;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class AggregatedCacheDataLoader implements CacheDataLoaderInterface
{
    use CacheDataLoaderTrait
    {
        clearCache as protected clearAdapterCache;
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

    public function clearCache(): bool
    {
        foreach ($this->getDataLoaders() as $dataLoader)
        {
            if (!$dataLoader->clearCache())
            {
                return false;
            }
        }

        return $this->clearCache();
    }

    protected function getDataForCache(): array
    {
        $data = [];

        foreach ($this->getDataLoaders() as $dataLoader)
        {
            $data = array_merge_recursive($data, $dataLoader->readData());
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
