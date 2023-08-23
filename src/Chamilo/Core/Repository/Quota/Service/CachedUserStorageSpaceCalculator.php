<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Traits\SingleCacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CachedUserStorageSpaceCalculator implements UserStorageSpaceCalculatorInterface
{
    use SingleCacheAdapterHandlerTrait;

    protected UserStorageSpaceCalculator $userStorageSpaceCalculator;

    public function __construct(
        AdapterInterface $cacheAdapter, UserStorageSpaceCalculator $userStorageSpaceCalculator
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->userStorageSpaceCalculator = $userStorageSpaceCalculator;
    }

    public function getAllowedStorageSpaceForUser(User $user): int
    {
        try
        {
            $cacheKey = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $user->getId()]);

            if (!$this->hasCacheDataForKey($cacheKey))
            {

                $this->saveCacheDataForKey(
                    $cacheKey, $this->getUserStorageSpaceCalculator()->getAllowedStorageSpaceForUser($user)
                );
            }

            return $this->readCacheDataForKey($cacheKey);
        }
        catch (CacheException $e)
        {
            return 0;
        }
    }

    public function getAvailableStorageSpaceForUser(User $user): int
    {
        $availableStorageSpace = $this->getAllowedStorageSpaceForUser($user) - $this->getUsedStorageSpaceForUser($user);

        return max($availableStorageSpace, 0);
    }

    public function getUsedAggregatedUserStorageSpace(): int
    {
        return $this->getUserStorageSpaceCalculator()->getUsedAggregatedUserStorageSpace();
    }

    public function getUsedStorageSpaceForUser(User $user): int
    {
        try
        {
            $cacheKey = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $user->getId()]);

            if (!$this->hasCacheDataForKey($cacheKey))
            {
                $this->saveCacheDataForKey(
                    $cacheKey, $this->getUserStorageSpaceCalculator()->getUsedStorageSpaceForUser($user)
                );
            }

            return $this->readCacheDataForKey($cacheKey);
        }
        catch (CacheException $e)
        {
            return 0;
        }
    }

    public function getUserStorageSpaceCalculator(): UserStorageSpaceCalculator
    {
        return $this->userStorageSpaceCalculator;
    }

    public function isQuotumDefinedForUser(User $user): bool
    {
        try
        {
            $cacheKey = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $user->getId()]);

            if (!$this->hasCacheDataForKey($cacheKey))
            {
                $this->saveCacheDataForKey(
                    $cacheKey, $this->getUserStorageSpaceCalculator()->isQuotumDefinedForUser($user)
                );
            }

            return $this->readCacheDataForKey($cacheKey);
        }
        catch (CacheException $e)
        {
            return true;
        }
    }
}