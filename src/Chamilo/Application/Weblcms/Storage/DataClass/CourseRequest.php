<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

/**
 *
 * @package application.lib.weblcms.course
 */
class CourseRequest extends CommonRequest
{
    const PROPERTY_COURSE_ID = 'course_id';

    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_COURSE_ID));
    }

    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    public function set_course_id($course_id)
    {
        return $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_course_request';
    }
}
