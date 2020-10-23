<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalculatorCacheService extends DoctrinePhpFileCacheService
{
    // Identifiers
    const IDENTIFIER_TOTAL_USER_DISK_QUOTA = 'total_user_disk_quota';

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Core\Repository\Quota';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array(self::IDENTIFIER_TOTAL_USER_DISK_QUOTA);
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function getTotalUserDiskQuota()
    {
        return $this->getForIdentifier(self::IDENTIFIER_TOTAL_USER_DISK_QUOTA);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $policy = Configuration::getInstance()->get_setting(array('Chamilo\Core\Repository', 'quota_policy'));
        $fallback = Configuration::getInstance()->get_setting(array('Chamilo\Core\Repository', 'quota_fallback'));

        if ($policy == Calculator::POLICY_USER && !$fallback)
        {
            $property = new FunctionConditionVariable(
                FunctionConditionVariable::SUM, new PropertyConditionVariable(User::class, User::PROPERTY_DISK_QUOTA),
                'disk_quota'
            );

            $parameters = new RecordRetrieveParameters(new DataClassProperties($property));

            $record = DataManager::record(User::class, $parameters);
            $totalQuota = $record['disk_quota'];
        }
        else
        {
            $users = DataManager::retrieves(User::class, new DataClassRetrievesParameters());

            $totalQuota = 0;

            foreach($users as $user)
            {
                $calculator = new Calculator($user);
                $totalQuota += $calculator->getMaximumUserDiskQuota();
            }

            $totalQuota;
        }

        return $this->getCacheProvider()->save($identifier, $totalQuota);
    }
}