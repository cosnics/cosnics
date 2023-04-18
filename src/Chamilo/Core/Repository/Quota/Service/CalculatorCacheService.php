<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Libraries\Cache\CacheDataLoaderTrait;
use Chamilo\Libraries\Cache\Interfaces\CacheDataAccessorInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalculatorCacheService implements CacheDataAccessorInterface
{
    use CacheDataLoaderTrait;

    protected StorageSpaceCalculator $storageSpaceCalculator;

    public function __construct(AdapterInterface $cacheAdapter, StorageSpaceCalculator $storageSpaceCalculator)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->storageSpaceCalculator = $storageSpaceCalculator;
    }

    public function getCacheKey(): string
    {
        return StorageSpaceCalculator::CACHE_KEY_MAXIMUM_AGGREGATED_USER_STORAGE_SPACE;
    }

    protected function getDataForCache(): int
    {
        return $this->getStorageSpaceCalculator()->getMaximumAggregatedUserStorageSpace();
    }

    public function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->storageSpaceCalculator;
    }
}