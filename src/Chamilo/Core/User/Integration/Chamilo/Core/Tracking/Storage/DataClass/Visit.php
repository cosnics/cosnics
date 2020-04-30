<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class tracks the visits to pages
 *
 * @package users.lib.trackers
 */
class Visit extends SimpleTracker
{
    const PROPERTY_ENTER_DATE = 'enter_date';

    const PROPERTY_LEAVE_DATE = 'leave_date';

    const PROPERTY_LOCATION = 'location';

    const PROPERTY_USER_ID = 'user_id';

    const TYPE_ENTER = 'enter';
    const TYPE_LEAVE = 'leave';

    public function run(array $parameters = array())
    {
        $this->validate_parameters($parameters);
        $type = $this->get_event()->getType();

        switch ($type)
        {
            default :
                break;
            case self::TYPE_ENTER :
                $this->track_enter($parameters);
                break;
            case self::TYPE_LEAVE :
                $this->track_leave($parameters);
                break;
        }
    }

    /**
     * Inherited
     *
     * @see MainTracker :: empty_tracker
     */
    public function empty_tracker()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TYPE),
            new StaticConditionVariable($this->get_event()->get_name())
        );

        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    public function export($start_date, $end_date, $event)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TYPE),
            new StaticConditionVariable($event->get_name())
        );

        return parent::export($start_date, $end_date, $conditions);
    }

    /**
     * Inherited
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_USER_ID,
                self::PROPERTY_ENTER_DATE,
                self::PROPERTY_LEAVE_DATE,
                self::PROPERTY_LOCATION
            )
        );
    }

    /**
     * Get's the enter date of the visit tracker
     *
     * @return int $date the date
     */
    public function get_enter_date()
    {
        return $this->get_default_property(self::PROPERTY_ENTER_DATE);
    }

    /**
     * Get's the leave date of the visit tracker
     *
     * @return int $date the date
     */
    public function get_leave_date()
    {
        return $this->get_default_property(self::PROPERTY_LEAVE_DATE);
    }

    /**
     * Get's the location of the visit tracker
     *
     * @return int $ip the ip
     */
    public function get_location()
    {
        return $this->get_default_property(self::PROPERTY_LOCATION);
    }

    /**
     * Get's the userid of the visit tracker
     *
     * @return int $userid the userid
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    private function init_enter()
    {
        $this->set_enter_date(time());
        $this->set_leave_date(time());
    }

    private function init_leave()
    {
        $this->set_leave_date(time());
    }

    /**
     * Sets the enter date of the visit tracker
     *
     * @param int $date the date
     */
    public function set_enter_date($value)
    {
        $this->set_default_property(self::PROPERTY_ENTER_DATE, $value);
    }

    /**
     * Sets the leave date of the visit tracker
     *
     * @param int $date the date
     */
    public function set_leave_date($value)
    {
        $this->set_default_property(self::PROPERTY_LEAVE_DATE, $value);
    }

    /**
     * Sets the location of the visit tracker
     *
     * @param int $ip the ip
     */
    public function set_location($value)
    {
        $this->set_default_property(self::PROPERTY_LOCATION, $value);
    }

    /**
     * Sets the userid of the visit tracker
     *
     * @param int $userid the userid
     */
    public function set_user_id($userid)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userid);
    }

    private function track_enter()
    {
        $this->init_enter();
        $success = $this->create();
        if ($success)
        {
            $tracker_id = $this->get_id();
            $html_header = "<script>var tracker={$tracker_id};</script>";
            Page::getInstance()->getHeader()->addHtmlHeader($html_header);
        }
    }

    private function track_leave()
    {
        $this->init_leave();

        return $this->update();
    }

    public function validate_parameters(array $parameters = array())
    {
        if (isset($parameters[self::PROPERTY_USER_ID]))
        {
            $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        }
        if (isset($parameters[self::PROPERTY_LOCATION]))
        {
            $this->set_location($parameters[self::PROPERTY_LOCATION]);
        }
        if (isset($parameters[self::PROPERTY_ID]))
        {
            $this->set_id($parameters[self::PROPERTY_ID]);
        }
    }
}
