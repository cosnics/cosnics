<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Traits\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Cache\Traits\SimpleCacheDataLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CachedAggregatedUserStorageSpaceCalculator
    implements CacheDataLoaderInterface, AggregatedUserStorageSpaceCalculatorInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataLoaderTrait;

    protected AggregatedUserStorageSpaceCalculator $aggregatedUserStorageSpaceCalculator;

    public function __construct(
        AdapterInterface $cacheAdapter, AggregatedUserStorageSpaceCalculator $aggregatedUserStorageSpaceCalculator
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->aggregatedUserStorageSpaceCalculator = $aggregatedUserStorageSpaceCalculator;
    }

    public function getAggregatedUserStorageSpaceCalculator(): AggregatedUserStorageSpaceCalculator
    {
        return $this->aggregatedUserStorageSpaceCalculator;
    }

    protected function getDataForCache(): int
    {
        return $this->getAggregatedUserStorageSpaceCalculator()->getMaximumAggregatedUserStorageSpace();
    }

    public function getMaximumAggregatedUserStorageSpace(): int
    {
        try
        {
            return $this->loadCacheData();
        }
        catch (CacheException $e)
        {
            return 0;
        }
    }
}