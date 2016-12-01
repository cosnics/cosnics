<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

/**
 *
 * @package application.lib.weblcms.trackers
 */
class UserStatusChange extends SimpleTracker
{
    const PROPERTY_USER_ID = 'user_id'; // by whom
    const PROPERTY_SUBJECT_ID = 'subject_id'; // to whom
    const PROPERTY_NEW_STATUS = 'new_status';
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_DATE = 'date';

    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_subject_id($parameters[self::PROPERTY_SUBJECT_ID]);
        $this->set_new_status($parameters[self::PROPERTY_NEW_STATUS]);
        $this->set_course_id($parameters[self::PROPERTY_COURSE_ID]);
        $this->set_date($parameters[self::PROPERTY_DATE]);
    }

    static function get_default_property_names()
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_USER_ID, 
                self::PROPERTY_SUBJECT_ID, 
                self::PROPERTY_COURSE_ID, 
                self::PROPERTY_NEW_STATUS, 
                self::PROPERTY_DATE));
    }

    function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    function get_subject_id()
    {
        return $this->get_default_property(self::PROPERTY_SUBJECT_ID);
    }

    function set_subject_id($subject_id)
    {
        $this->set_default_property(self::PROPERTY_SUBJECT_ID, $subject_id);
    }

    function get_new_status()
    {
        $this->get_default_property(self::PROPERTY_NEW_STATUS);
    }

    function set_new_status($new_status)
    {
        $this->set_default_property(self::PROPERTY_NEW_STATUS, $new_status);
    }

    function get_course_id()
    {
        $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    function get_date()
    {
        $this->get_default_property(self::PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self::PROPERTY_DATE, $date);
    }
}
?>
