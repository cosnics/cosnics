<?php
namespace Chamilo\Libraries\Platform\Configuration\Cache;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;
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
     * @param string $identifier
     *
     * @return bool
     */
    public function clearForIdentifier($identifier)
    {
        $this->getDataClassRepositoryCache()->truncate(UserSetting::class);

        return parent::clearForIdentifier($identifier);
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
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            DataClassRepositoryCache::class
        );
    }

    /**
     *
     * @param integer $userIdentifier
     *
     * @return boolean[]
     */
    public function getForUserIdentifier($userIdentifier)
    {
        return $this->getForIdentifier($userIdentifier);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return DataManager::distinct(
            User::class, new DataClassDistinctParameters(
                null, new DataClassProperties(array(new PropertyConditionVariable(User::class, User::PROPERTY_ID)))
            )
        );
    }

    /**
     * @param string $identifier
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function warmUpForIdentifier($identifier)
    {
        $localSettings = array();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_USER_ID),
            new StaticConditionVariable($identifier)
        );
        $userSettings = \Chamilo\Core\User\Storage\DataManager::retrieves(
            UserSetting::class, new DataClassRetrievesParameters($condition)
        );

        foreach ($userSettings as $userSetting)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Setting::class, Setting::PROPERTY_ID),
                new StaticConditionVariable($userSetting->get_setting_id())
            );
            /**
             * @var \Chamilo\Configuration\Storage\DataClass\Setting $setting
             */
            $setting = \Chamilo\Configuration\Storage\DataManager::retrieve(
                Setting::class, new DataClassRetrieveParameters($condition)
            );
            $localSettings[$setting->get_context()][$setting->get_variable()] = $userSetting->get_value();
        }

        return $this->getCacheProvider()->save($identifier, $localSettings);
    }
}