<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Configuration\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Setting extends DataClass
{

    /**
     *
     * @deprecated Use PROPERTY_CONTEXT instead
     */
    const PROPERTY_APPLICATION = 'context';
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_USER_SETTING = 'user_setting';

    /**
     * Get the default properties of all settings.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_CONTEXT, self::PROPERTY_VARIABLE, self::PROPERTY_VALUE, self::PROPERTY_USER_SETTING));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the application of this setting object
     *
     * @return string The setting application
     * @deprecated Use get_context instead
     */
    public function get_application()
    {
        return $this->get_context();
    }

    public function get_context()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT);
    }

    /**
     * Returns the variable of this setting object
     *
     * @return string the variable
     */
    public function get_variable()
    {
        return $this->get_default_property(self::PROPERTY_VARIABLE);
    }

    /**
     * Returns the value of this setting object
     *
     * @return string the value
     */
    public function get_value()
    {
        return $this->get_default_property(self::PROPERTY_VALUE);
    }

    /**
     * Sets the application of this setting.
     *
     * @param $application string the setting application.
     * @deprecated Use set_context instead
     */
    public function set_application($application)
    {
        $this->set_context($application);
    }

    public function set_context($context)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the variable of this setting.
     *
     * @param $variable string the variable.
     */
    public function set_variable($variable)
    {
        $this->set_default_property(self::PROPERTY_VARIABLE, $variable);
    }

    /**
     * Sets the value of this setting.
     *
     * @param $value string the value.
     */
    public function set_value($value)
    {
        $this->set_default_property(self::PROPERTY_VALUE, $value);
    }

    /**
     * Returns the user_setting of this setting object
     *
     * @return string the user_setting
     */
    public function get_user_setting()
    {
        return $this->get_default_property(self::PROPERTY_USER_SETTING);
    }

    /**
     * Sets the user_setting of this setting.
     *
     * @param $user_setting string the user_setting.
     */
    public function set_user_setting($user_setting)
    {
        $this->set_default_property(self::PROPERTY_USER_SETTING, $user_setting);
    }

    public function delete()
    {
        if (! parent::delete())
        {
            return false;
        }
        else
        {
            if ($this->get_user_setting())
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
                    new StaticConditionVariable($this->get_id()));
                if (! \Chamilo\Core\User\Storage\DataManager::deletes(UserSetting::class, $condition))
                {
                    return false;
                }
                else
                {
                    $this->on_change();
                    return true;
                }
            }
            else
            {
                $this->on_change();
                return true;
            }
        }
    }

    public function create()
    {
        return $this->on_change(parent::create());
    }

    public function update()
    {
        return $this->on_change(parent::update());
    }

    protected function on_change($success = true)
    {
        if (! $success)
        {
            return $success;
        }

        Configuration::getInstance()->reset();
        return $success;
    }
}