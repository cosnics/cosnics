<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Libraries\Cache\Interfaces\CacheDataAccessorInterface;
use Chamilo\Libraries\Cache\Traits\SingularCacheDataAccessorTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class AggregatedCacheDataLoader implements CacheDataAccessorInterface
{
    use SingularCacheDataAccessorTrait
    {
        clearCacheData as protected clearAdapterCache;
    }

    /**
     * @var \Chamilo\Libraries\Cache\Interfaces\CacheDataAccessorInterface[]
     */
    private array $dataLoaders;

    /**
     * @param \Chamilo\Libraries\Cache\Interfaces\CacheDataAccessorInterface[] $dataLoaders
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
            $data = array_merge_recursive($data, $dataLoader->loadData());
        }

        return $data;
    }

    /**
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheDataAccessorInterface[]
     */
    protected function getDataLoaders(): array
    {
        return $this->dataLoaders;
    }
}
