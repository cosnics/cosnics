<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class LoginLogout extends SimpleTracker
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_IP = 'ip';
    const PROPERTY_TYPE = 'type';

    public function validate_parameters(array $parameters = array())
    {
        $user = $parameters['user'];
        $server = $parameters['server'];
        
        $this->set_user_id($user->get_id());
        $this->set_date(time());
        $this->set_ip($server['REMOTE_ADDR']);
        $this->set_type($this->get_event()->getType());
    }

    public function empty_tracker($event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TYPE),
            new StaticConditionVariable($event->get_name()));
        return $this->remove($condition);
    }

    public function export($start_date, $end_date, $event)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TYPE),
            new StaticConditionVariable($event->get_name()));
        return parent::export($start_date, $end_date, $conditions);
    }

    /**
     * Get's the userid of the login tracker
     * 
     * @return int $userid the userid
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * Sets the userid of the login tracker
     * 
     * @param int $userid the userid
     */
    public function set_user_id($userid)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userid);
    }

    /**
     * Get's the date of the login tracker
     * 
     * @return int $date the date
     */
    public function get_date()
    {
        return $this->get_default_property(self::PROPERTY_DATE);
    }

    /**
     * Sets the date of the login tracker
     * 
     * @param int $date the date
     */
    public function set_date($date)
    {
        $this->set_default_property(self::PROPERTY_DATE, $date);
    }

    /**
     * Get's the ip of the login tracker
     * 
     * @return int $ip the ip
     */
    public function get_ip()
    {
        return $this->get_default_property(self::PROPERTY_IP);
    }

    /**
     * Sets the ip of the login tracker
     * 
     * @param int $ip the ip
     */
    public function set_ip($ip)
    {
        $this->set_default_property(self::PROPERTY_IP, $ip);
    }

    /**
     * Get's the type of the login tracker
     * 
     * @return int $type the type
     */
    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     * Sets the type of the login tracker
     * 
     * @param int $type the type
     */
    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_TYPE, self::PROPERTY_USER_ID, self::PROPERTY_DATE, self::PROPERTY_IP));
    }
}
