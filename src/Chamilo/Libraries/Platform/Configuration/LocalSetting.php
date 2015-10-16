<?php
namespace Chamilo\Libraries\Platform\Configuration;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\File\Cache\FilesystemCache;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * $Id: local_setting.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 *
 * @package common.configuration
 */
/**
 * This class represents the current configurable settings.
 *
 * @author Sven Vanpoucke
 */
class LocalSetting
{

    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Parameters defined in the configuration. Stored as an associative array.
     */
    private $params;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->params = $this->load_local_settings();
    }

    /**
     * reload the instance
     */
    public static function reload()
    {
        $instance = self :: get_instance();
        $instance->params = $instance->load_local_settings();
    }

    /**
     * Returns the instance of this class.
     *
     * @return LocalSetting The instance.
     */
    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Returns the params
     *
     * @return array null
     */
    public function get_params()
    {
        return $this->params;
    }

    /**
     * Gets a parameter from the configuration.
     *
     * @param $section string The name of the section in which the parameter is located.
     * @param $name string The parameter name.
     * @return mixed The parameter value.
     */
    public static function get($variable, $application = 'Chamilo\Core\Admin')
    {
        $instance = self :: get_instance();

        $params = & $instance->get_params();

        if (! $params)
        {
            return PlatformSetting :: get($variable, $application);
        }

        if (isset($params[$application]) && isset($params[$application][$variable]))
        {
            return $params[$application][$variable];
        }
        else
        {
            return PlatformSetting :: get($variable, $application);
        }
    }

    public function load_local_settings()
    {
        $user_id = Session :: get_user_id();

        if (! $user_id)
        {
            return null;
        }

        $cache = new FilesystemCache(Path :: getInstance()->getCachePath(__NAMESPACE__));
        $cacheIdentifier = md5(serialize(array(__METHOD__, $user_id)));

        if (! $cache->contains($cacheIdentifier))
        {
            $params = array();

            $condition = new EqualityCondition(
                new PropertyConditionVariable(UserSetting :: class_name(), UserSetting :: PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));
            $user_settings = \Chamilo\Core\User\Storage\DataManager :: retrieves(
                UserSetting :: class_name(),
                new DataClassRetrievesParameters($condition));

            while ($user_setting = $user_settings->next_result())
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_ID),
                    new StaticConditionVariable($user_setting->get_setting_id()));
                $setting = \Chamilo\Configuration\Storage\DataManager :: retrieve(
                    Setting :: class_name(),
                    new DataClassRetrieveParameters($condition));
                $params[$setting->get_application()][$setting->get_variable()] = $user_setting->get_value();
            }

            $cache->save($cacheIdentifier, $params);
        }

        return $cache->fetch($cacheIdentifier);
    }

    public static function create_local_setting($variable, $value, $application = 'Chamilo\Core\Admin', $user_id = null)
    {
        if (! $user_id)
        {
            $user_id = Session :: get_user_id();
        }

        $setting = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_from_variable_name(
            $variable,
            $application);

        if ($setting && $setting->get_user_setting() == 1)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(UserSetting :: class_name(), UserSetting :: PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(UserSetting :: class_name(), UserSetting :: PROPERTY_SETTING_ID),
                new StaticConditionVariable($setting->get_id()));
            $condition = new AndCondition($conditions);

            $user_setting = \Chamilo\Core\User\Storage\DataManager :: retrieve(
                UserSetting :: class_name(),
                new DataClassRetrieveParameters($condition));

            if ($user_setting)
            {
                $user_setting->set_value($value);
                return $user_setting->update();
            }
            else
            {
                $user_setting = new UserSetting();
                $user_setting->set_setting_id($setting->get_id());
                $user_setting->set_user_id($user_id);
                $user_setting->set_value($value);
                return $user_setting->create();
            }
        }
    }
}
