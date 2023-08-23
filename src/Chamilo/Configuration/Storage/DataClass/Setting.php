<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Configuration\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Setting extends DataClass
{
    public const CONTEXT = 'Chamilo\Configuration';

    public const PROPERTY_APPLICATION = 'context';
    public const PROPERTY_CONTEXT = 'context';
    public const PROPERTY_USER_SETTING = 'user_setting';
    public const PROPERTY_VALUE = 'value';
    public const PROPERTY_VARIABLE = 'variable';

    public function create(): bool
    {
        return $this->on_change(parent::create());
    }

    public function delete(): bool
    {
        if (!parent::delete())
        {
            return false;
        }
        elseif ($this->get_user_setting())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(UserSetting::class, UserSetting::PROPERTY_SETTING_ID),
                new StaticConditionVariable($this->get_id())
            );
            if (!DataManager::deletes(UserSetting::class, $condition))
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

    /**
     * Get the default properties of all settings.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_CONTEXT, self::PROPERTY_VARIABLE, self::PROPERTY_VALUE, self::PROPERTY_USER_SETTING]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_setting';
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
        return $this->getDefaultProperty(self::PROPERTY_CONTEXT);
    }

    /**
     * Returns the user_setting of this setting object
     *
     * @return string the user_setting
     */
    public function get_user_setting()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_SETTING);
    }

    /**
     * Returns the value of this setting object
     *
     * @return string the value
     */
    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    /**
     * Returns the variable of this setting object
     *
     * @return string the variable
     */
    public function get_variable()
    {
        return $this->getDefaultProperty(self::PROPERTY_VARIABLE);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function on_change($success = true)
    {
        if (!$success)
        {
            return $success;
        }

        /**
         * @var \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
         */
        $configurationConsulter = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurationConsulter::class
        );

        $configurationConsulter->getDataPreLoader()->clearCacheData();

        return $success;
    }

    /**
     * Sets the application of this setting.
     *
     * @param $application string the setting application.
     *
     * @deprecated Use set_context instead
     */
    public function set_application($application)
    {
        $this->set_context($application);
    }

    public function set_context($context)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Sets the user_setting of this setting.
     *
     * @param $user_setting string the user_setting.
     */
    public function set_user_setting($user_setting)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_SETTING, $user_setting);
    }

    /**
     * Sets the value of this setting.
     *
     * @param $value string the value.
     */
    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }

    /**
     * Sets the variable of this setting.
     *
     * @param $variable string the variable.
     */
    public function set_variable($variable)
    {
        $this->setDefaultProperty(self::PROPERTY_VARIABLE, $variable);
    }

    public function update(): bool
    {
        return $this->on_change(parent::update());
    }
}