<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

/**
 * $Id: course_request.class.php 216 2010-02-25 11:06:00Z Yannick & Tristan$
 * 
 * @package application.lib.weblcms.course
 */
class CourseRequest extends CommonRequest
{
    const PROPERTY_COURSE_ID = 'course_id';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_COURSE_ID));
    }

    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    public function set_course_id($course_id)
    {
        return $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }
}
