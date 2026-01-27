<?php
namespace Chamilo\Core\Admin\Service\DataLoader;

use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Storage\Architecture\Trait\SimpleCacheDataPreLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Admin\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class AggregatedCacheDataPreLoader implements CacheDataPreLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataPreLoaderTrait;

    /**
     * @var \Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface[]
     */
    private array $dataPreLoaders;

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface[] $dataPreLoaders
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
            $data = array_merge_recursive($data, $dataPreLoader->preLoadCacheData());
        }

        return $data;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface[]
     */
    protected function getDataPreLoaders(): array
    {
        return $this->dataPreLoaders;
    }
}
