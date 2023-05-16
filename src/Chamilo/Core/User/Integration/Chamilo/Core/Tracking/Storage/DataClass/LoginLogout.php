<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

class LoginLogout extends SimpleTracker
{
    public const CONTEXT = 'Chamilo\Core\User\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_DATE = 'date';
    public const PROPERTY_IP = 'ip';
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_USER_ID = 'user_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_TYPE, self::PROPERTY_USER_ID, self::PROPERTY_DATE, self::PROPERTY_IP]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_user_login_logout';
    }

    /**
     * Get's the date of the login tracker
     *
     * @return int $date the date
     */
    public function get_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    /**
     * Get's the ip of the login tracker
     *
     * @return int $ip the ip
     */
    public function get_ip()
    {
        return $this->getDefaultProperty(self::PROPERTY_IP);
    }

    /**
     * Get's the type of the login tracker
     *
     * @return int $type the type
     */
    public function get_type()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    /**
     * Get's the userid of the login tracker
     *
     * @return int $userid the userid
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    /**
     * Sets the date of the login tracker
     *
     * @param int $date the date
     */
    public function set_date($date)
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);
    }

    /**
     * Sets the ip of the login tracker
     *
     * @param int $ip the ip
     */
    public function set_ip($ip)
    {
        $this->setDefaultProperty(self::PROPERTY_IP, $ip);
    }

    /**
     * @deprecated Use LoginLogout::setType()
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    /**
     * Sets the userid of the login tracker
     *
     * @param int $userid the userid
     */
    public function set_user_id($userid)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userid);
    }

    public function validate_parameters(array $parameters = [])
    {
        $user = $parameters['user'];
        $server = $parameters['server'];

        $this->set_user_id($user->get_id());
        $this->set_date(time());
        $this->set_ip($server['REMOTE_ADDR']);
        $this->setType($this->get_event()->getType());
    }
}
