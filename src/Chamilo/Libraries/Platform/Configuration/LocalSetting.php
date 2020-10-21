<?php
namespace Chamilo\Libraries\Platform\Configuration;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Platform\Configuration
 *
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LocalSetting
{

    /**
     * Instance of this class for the singleton pattern.
     *
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    private static $instance;

    /**
     * Parameters defined in the configuration.
     * Stored as an associative array.
     *
     * @var string[]
     */
    private $localSettings;

    /**
     *
     * @var integer
     */
    private $currentUserIdentifier;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService
     */
    private $localSettingCacheService;

    /**
     *
     * @param \Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService $localSettingCacheService
     * @param integer $currentUserIdentifier
     */
    public function __construct(LocalSettingCacheService $localSettingCacheService, $currentUserIdentifier = 0)
    {
        $this->localSettingCacheService = $localSettingCacheService;
        $this->currentUserIdentifier = $currentUserIdentifier;
        $this->localSettings = $localSettingCacheService->getForUserIdentifier($currentUserIdentifier);
    }

    /**
     * @param string $variable
     * @param string $value
     * @param string $application
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return bool
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function create($variable, $value, $application = 'Chamilo\Core\Admin', User $user = null)
    {
        $userIdentifier = $user instanceof User ? $user->getId() : $this->currentUserIdentifier;

        $setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
            $variable, $application
        );

        if ($setting && $setting->get_user_setting() == 1)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_USER_ID),
                new StaticConditionVariable($userIdentifier)
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
                new StaticConditionVariable($setting->get_id())
            );
            $condition = new AndCondition($conditions);

            /**
             * @var \Chamilo\Core\User\Storage\DataClass\UserSetting $user_setting
             */
            $user_setting = DataManager::retrieve(
                UserSetting::class, new DataClassRetrieveParameters($condition)
            );

            if ($user_setting)
            {
                $user_setting->set_value($value);
                $result = $user_setting->update();
            }
            else
            {
                $user_setting = new UserSetting();
                $user_setting->set_setting_id($setting->get_id());
                $user_setting->set_user_id($userIdentifier);
                $user_setting->set_value($value);
                $result = $user_setting->create();
            }

            if (!$result)
            {
                return false;
            }

            return $this->getLocalSettingCacheService()->clearAndWarmUpForIdentifiers(array($userIdentifier));
        }

        return false;
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param string $variable
     * @param string $application
     * @param \Chamilo\Core\User\Storage\DataClass\User|null $user
     *
     * @return string The parameter value.
     */
    public function get($variable, $application = 'Chamilo\Core\Admin', User $user = null)
    {
        if ($user instanceof User)
        {
            $localSettings = $this->getLocalSettingCacheService()->getForUserIdentifier($user->getId());
        }
        else
        {
            $localSettings = $this->getLocalSettings();
        }

        if (!$localSettings)
        {
            return Configuration::getInstance()->get_setting(array($application, $variable));
        }

        if (isset($localSettings[$application]) && isset($localSettings[$application][$variable]))
        {
            return $localSettings[$application][$variable];
        }
        else
        {
            return Configuration::getInstance()->get_setting(array($application, $variable));
        }
    }

    /**
     *
     * @return integer
     */
    public function getCurrentUserIdentifier()
    {
        return $this->currentUserIdentifier;
    }

    /**
     *
     * @param number $currentUserIdentifier
     */
    public function setCurrentUserIdentifier($currentUserIdentifier)
    {
        $this->currentUserIdentifier = $currentUserIdentifier;
    }

    /**
     * Returns the instance of this class.
     *
     * @return \Chamilo\Libraries\Platform\Configuration\LocalSetting The instance.
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $configurablePathBuilder = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                ConfigurablePathBuilder::class
            );

            $localSettingCacheService = new LocalSettingCacheService($configurablePathBuilder);
            $userIdentifier = Session::get_user_id();
            if (is_null($userIdentifier))
            {
                $userIdentifier = 0;
            }
            self::$instance = new self($localSettingCacheService, $userIdentifier);
        }

        return self::$instance;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService
     */
    public function getLocalSettingCacheService()
    {
        return $this->localSettingCacheService;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService $localSettingCacheService
     */
    public function setLocalSettingCacheService($localSettingCacheService)
    {
        $this->localSettingCacheService = $localSettingCacheService;
    }

    /**
     * Returns the localSettings
     *
     * @return string[]
     */
    public function getLocalSettings()
    {
        return $this->localSettings;
    }

    /**
     *
     * @param string[] $localSettings
     */
    public function setLocalSettings($localSettings)
    {
        $this->localSettings = $localSettings;
    }

    /**
     * Resets the local settings cache for the current instance
     */
    public function resetCache()
    {
        $this->getLocalSettingCacheService()->clearForIdentifier($this->currentUserIdentifier);
        DataClassCache::truncate(UserSetting::class);
        $this->localSettings = $this->getLocalSettingCacheService()->getForUserIdentifier($this->currentUserIdentifier);
    }
}
