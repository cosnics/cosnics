<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

/**
 * @package application.lib.weblcms.trackers
 */
class UserStatusChange extends SimpleTracker
{
    public const CONTEXT = 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_COURSE_ID = 'course_id'; // by whom
    public const PROPERTY_DATE = 'date'; // to whom
    public const PROPERTY_NEW_STATUS = 'new_status';
    public const PROPERTY_SUBJECT_ID = 'subject_id';
    public const PROPERTY_USER_ID = 'user_id';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_USER_ID,
                self::PROPERTY_SUBJECT_ID,
                self::PROPERTY_COURSE_ID,
                self::PROPERTY_NEW_STATUS,
                self::PROPERTY_DATE
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_weblcms_user_status_change';
    }

    public function get_course_id()
    {
        $this->getDefaultProperty(self::PROPERTY_COURSE_ID);
    }

    public function get_date()
    {
        $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    public function get_new_status()
    {
        $this->getDefaultProperty(self::PROPERTY_NEW_STATUS);
    }

    public function get_subject_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_SUBJECT_ID);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_course_id($course_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_ID, $course_id);
    }

    public function set_date($date)
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);
    }

    public function set_new_status($new_status)
    {
        $this->setDefaultProperty(self::PROPERTY_NEW_STATUS, $new_status);
    }

    public function set_subject_id($subject_id)
    {
        $this->setDefaultProperty(self::PROPERTY_SUBJECT_ID, $subject_id);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    public function validate_parameters(array $parameters = [])
    {
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_subject_id($parameters[self::PROPERTY_SUBJECT_ID]);
        $this->set_new_status($parameters[self::PROPERTY_NEW_STATUS]);
        $this->set_course_id($parameters[self::PROPERTY_COURSE_ID]);
        $this->set_date($parameters[self::PROPERTY_DATE]);
    }
}
