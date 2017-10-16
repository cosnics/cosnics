<?php

namespace Chamilo\Libraries\Platform\Configuration;

use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Configuration\Configuration;

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
    private $userIdentifier;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService
     */
    private $localSettingCacheService;

    /**
     *
     * @param \Chamilo\Libraries\Platform\Configuration\Cache\LocalSettingCacheService $localSettingCacheService
     * @param integer $userIdentifier
     */
    public function __construct(LocalSettingCacheService $localSettingCacheService, $userIdentifier = 0)
    {
        $this->localSettingCacheService = $localSettingCacheService;
        $this->userIdentifier = $userIdentifier;
        $this->localSettings = $localSettingCacheService->getForUserIdentifier($userIdentifier);
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
     *
     * @return number
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     *
     * @param number $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
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
            $localSettingCacheService = new LocalSettingCacheService();
            $userIdentifier = Session::get_user_id();
            self::$instance = new self($localSettingCacheService, $userIdentifier);
        }

        return self::$instance;
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
     * Gets a parameter from the configuration.
     *
     * @param $variable
     * @param string $application
     *
     * @return string The parameter value.
     */
    public function get($variable, $application = 'Chamilo\Core\Admin')
    {
        $localSettings = $this->getLocalSettings();

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
     * @param string $variable
     * @param string $value
     * @param string $application
     *
     * @return bool
     */
    public function create($variable, $value, $application = 'Chamilo\Core\Admin')
    {
        $setting = \Chamilo\Configuration\Storage\DataManager::retrieve_setting_from_variable_name(
            $variable,
            $application
        );

        if ($setting && $setting->get_user_setting() == 1)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(UserSetting::class_name(), UserSetting::PROPERTY_USER_ID),
                new StaticConditionVariable($this->getUserIdentifier())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(UserSetting::class_name(), UserSetting::PROPERTY_SETTING_ID),
                new StaticConditionVariable($setting->get_id())
            );
            $condition = new AndCondition($conditions);

            /** @var UserSetting $user_setting */
            $user_setting = \Chamilo\Core\User\Storage\DataManager::retrieve(
                UserSetting::class_name(),
                new DataClassRetrieveParameters($condition)
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
                $user_setting->set_user_id($this->getUserIdentifier());
                $user_setting->set_value($value);
                $result = $user_setting->create();
            }

            if (!$result)
            {
                return false;
            }

            return $this->getLocalSettingCacheService()->clearAndWarmUpForIdentifiers(
                array($this->getUserIdentifier())
            );
        }

        return false;
    }

    /**
     * Resets the local settings cache for the current instance
     */
    public function resetCache()
    {
        $this->getLocalSettingCacheService()->clearForIdentifier($this->userIdentifier);
        DataClassCache::truncate(UserSetting::class_name());
        $this->localSettings = $this->getLocalSettingCacheService()->getForUserIdentifier($this->userIdentifier);
    }
}
