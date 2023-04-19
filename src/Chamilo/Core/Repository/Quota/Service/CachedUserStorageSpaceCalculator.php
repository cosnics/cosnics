<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Traits\CacheAdapterHandlerTrait;
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
    use CacheAdapterHandlerTrait;

    protected UserStorageSpaceCalculator $allowedUserStorageSpaceCalculator;

    public function __construct(
        AdapterInterface $cacheAdapter, UserStorageSpaceCalculator $allowedUserStorageSpaceCalculator
    )
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->allowedUserStorageSpaceCalculator = $allowedUserStorageSpaceCalculator;
    }

    public function getAllowedStorageSpaceForUser(User $user): int
    {
        try
        {
            $cacheKey = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $user->getId()]);

            if (!$this->hasCacheData($cacheKey))
            {

                $this->saveCacheData(
                    $cacheKey, $this->getAllowedUserStorageSpaceCalculator()->getAllowedStorageSpaceForUser($user)
                );
            }

            return $this->loadCacheData($cacheKey);
        }
        catch (CacheException $e)
        {
            return 0;
        }
    }

    public function getAllowedUserStorageSpaceCalculator(): UserStorageSpaceCalculator
    {
        return $this->allowedUserStorageSpaceCalculator;
    }

    public function getAvailableStorageSpaceForUser(User $user): int
    {
        $availableStorageSpace = $this->getAllowedStorageSpaceForUser($user) - $this->getUsedStorageSpaceForUser($user);

        return max($availableStorageSpace, 0);
    }

    public function getUsedAggregatedUserStorageSpace(): int
    {
        return $this->getAllowedUserStorageSpaceCalculator()->getUsedAggregatedUserStorageSpace();
    }

    public function getUsedStorageSpaceForUser(User $user): int
    {
        try
        {
            $cacheKey = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $user->getId()]);

            if (!$this->hasCacheData($cacheKey))
            {
                $this->saveCacheData(
                    $cacheKey, $this->getAllowedUserStorageSpaceCalculator()->getUsedStorageSpaceForUser($user)
                );
            }

            return $this->loadCacheData($cacheKey);
        }
        catch (CacheException $e)
        {
            return 0;
        }
    }

    public function isQuotumDefinedForUser(User $user): bool
    {
        try
        {
            $cacheKey = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $user->getId()]);

            if (!$this->hasCacheData($cacheKey))
            {
                $this->saveCacheData(
                    $cacheKey, $this->getAllowedUserStorageSpaceCalculator()->isQuotumDefinedForUser($user)
                );
            }

            return $this->loadCacheData($cacheKey);
        }
        catch (CacheException $e)
        {
            return true;
        }
    }
}