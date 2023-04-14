<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Configuration\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalculatorCacheService extends DoctrineCacheService
{
    public const IDENTIFIER_TOTAL_USER_DISK_QUOTA = 'total_user_disk_quota';

    public function getIdentifiers(): array
    {
        return [self::IDENTIFIER_TOTAL_USER_DISK_QUOTA];
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getTotalUserDiskQuota(): int
    {
        return $this->getForIdentifier(self::IDENTIFIER_TOTAL_USER_DISK_QUOTA);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUpForIdentifier($identifier): bool
    {
        $policy = Configuration::getInstance()->get_setting(['Chamilo\Core\Repository', 'quota_policy']);
        $fallback = Configuration::getInstance()->get_setting(['Chamilo\Core\Repository', 'quota_fallback']);

        if ($policy == Calculator::POLICY_USER && !$fallback)
        {
            $property = new FunctionConditionVariable(
                FunctionConditionVariable::SUM, new PropertyConditionVariable(User::class, User::PROPERTY_DISK_QUOTA),
                'disk_quota'
            );

            $parameters = new RecordRetrieveParameters(new RetrieveProperties($property));

            $record = DataManager::record(User::class, $parameters);
            $totalQuota = $record['disk_quota'];
        }
        else
        {
            $users = DataManager::retrieves(User::class, new DataClassRetrievesParameters());

            $totalQuota = 0;

            foreach ($users as $user)
            {
                $calculator = new Calculator($user);
                $totalQuota += $calculator->getMaximumUserDiskQuota();
            }
        }

        $cacheItem = $this->getCacheAdapter()->getItem($identifier);
        $cacheItem->set($totalQuota);

        return $this->getCacheAdapter()->save($cacheItem);
    }
}