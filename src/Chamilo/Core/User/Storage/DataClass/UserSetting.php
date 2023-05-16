<?php
namespace Chamilo\Core\User\Storage\DataClass;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @author  Sven Vanpoucke
 * @package user.lib
 */
class UserSetting extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_SETTING_ID = 'setting_id';
    public const PROPERTY_USER_ID = 'user_id';
    public const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties of all users quota objects.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_USER_ID, self::PROPERTY_SETTING_ID, self::PROPERTY_VALUE]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'user_user_setting';
    }

    public function get_setting_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_SETTING_ID);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    public function set_setting_id($setting_id)
    {
        $this->setDefaultProperty(self::PROPERTY_SETTING_ID, $setting_id);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }
}
