<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Interfaces\CacheDataReaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheDataLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class AggregatedCacheDataLoader implements CacheDataLoaderInterface, CacheDataReaderInterface
{
    use CacheDataLoaderTrait;

    /**
     * @var \Chamilo\Libraries\Cache\Interfaces\CacheDataReaderInterface[]|\Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface[]
     */
    private array $dataReaders;

    /**
     * @param \Chamilo\Libraries\Cache\Interfaces\CacheDataReaderInterface[]|\Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface[] $dataReaders
     */
    public function __construct(AdapterInterface $cacheAdapter, array $dataReaders = [])
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->dataReaders = $dataReaders;
    }

    protected function getDataForCache(): array
    {
        $data = [];

        foreach ($this->getDataReaders() as $dataLoader)
        {
            $data = array_merge_recursive($data, $dataLoader->readCacheData());
        }

        return $data;
    }

    /**
     * @return \Chamilo\Libraries\Cache\Interfaces\CacheDataReaderInterface[]|\Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface[]
     */
    protected function getDataReaders(): array
    {
        return $this->dataReaders;
    }
}
