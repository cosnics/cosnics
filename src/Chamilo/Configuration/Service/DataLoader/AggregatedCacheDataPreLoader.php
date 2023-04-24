<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataPreLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class AggregatedCacheDataPreLoader implements CacheDataPreLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataPreLoaderTrait;

    /**
     * @var \Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface[]
     */
    private array $dataPreLoaders;

    /**
     * @param \Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface[] $dataPreLoaders
     */
    public function __construct(AdapterInterface $cacheAdapter, array $dataPreLoaders = [])
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->dataPreLoaders = $dataPreLoaders;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function getDataForCache(): array
    {
        $data = [];

        foreach ($this->getDataPreLoaders() as $dataPreLoader)
        {
            $data = array_merge_recursive($data, $dataPreLoader->preLoadCachedData());
        }

        return $data;
    }

    /**
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheDataPreLoaderInterface[]
     */
    protected function getDataPreLoaders(): array
    {
        return $this->dataPreLoaders;
    }
}
