<?php
namespace Chamilo\Libraries\Platform\Configuration\Cache;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class LocalSettingCacheService extends DoctrineCacheService implements UserBasedCacheInterface
{

    public function clearForIdentifier($identifier): bool
    {
        $this->getDataClassRepositoryCache()->truncate(UserSetting::class);

        return parent::clearForIdentifier($identifier);
    }

    protected function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            DataClassRepositoryCache::class
        );
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getForUserIdentifier($userIdentifier): bool
    {
        return $this->getForIdentifier($userIdentifier);
    }

    /**
     * @throws \Exception
     */
    public function getIdentifiers(): array
    {
        return DataManager::distinct(
            User::class, new DataClassDistinctParameters(
                null, new RetrieveProperties([new PropertyConditionVariable(User::class, User::PROPERTY_ID)])
            )
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function warmUpForIdentifier($identifier): bool
    {
        $localSettings = [];

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
                new PropertyConditionVariable(Setting::class, DataClass::PROPERTY_ID),
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

        $cacheItem = $this->getCacheAdapter()->getItem($identifier);
        $cacheItem->set($localSettings);

        return $this->getCacheAdapter()->save($cacheItem);
    }
}