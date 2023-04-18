<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Libraries\Cache\Interfaces\CacheDataAccessorInterface;
use Chamilo\Libraries\Cache\Traits\SingularCacheDataAccessorTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalculatorCacheService implements CacheDataAccessorInterface
{
    use SingularCacheDataAccessorTrait;

    protected StorageSpaceCalculator $storageSpaceCalculator;

    public function __construct(AdapterInterface $cacheAdapter, StorageSpaceCalculator $storageSpaceCalculator)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->storageSpaceCalculator = $storageSpaceCalculator;
    }

    protected function getDataForCache(): int
    {
        return $this->getStorageSpaceCalculator()->doGetMaximumAggregatedUserStorageSpace();
    }

    public function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->storageSpaceCalculator;
    }
}