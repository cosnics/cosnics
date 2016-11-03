<?php
namespace Chamilo\Libraries\Platform\Configuration\Cache;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LocalSettingCacheService extends DoctrinePhpFileCacheService implements UserBasedCacheInterface
{

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $localSettings = array();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserSetting :: class_name(), UserSetting :: PROPERTY_USER_ID),
            new StaticConditionVariable($identifier));
        $userSettings = \Chamilo\Core\User\Storage\DataManager :: retrieves(
            UserSetting :: class_name(),
            new DataClassRetrievesParameters($condition));

        while ($userSetting = $userSettings->next_result())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_ID),
                new StaticConditionVariable($userSetting->get_setting_id()));
            $setting = \Chamilo\Configuration\Storage\DataManager :: retrieve(
                Setting :: class_name(),
                new DataClassRetrieveParameters($condition));
            $localSettings[$setting->get_application()][$setting->get_variable()] = $userSetting->get_value();
        }

        return $this->getCacheProvider()->save($identifier, $localSettings);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Libraries\Platform\Configuration';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return \Chamilo\Libraries\Storage\DataManager\DataManager :: distinct(
            User :: class_name(),
            new DataClassDistinctParameters(null, User :: PROPERTY_ID));
    }

    /**
     *
     * @param integer $userIdentifier
     * @return boolean[]
     */
    public function getForUserIdentifier($userIdentifier)
    {
        return $this->getForIdentifier($userIdentifier);
    }
}