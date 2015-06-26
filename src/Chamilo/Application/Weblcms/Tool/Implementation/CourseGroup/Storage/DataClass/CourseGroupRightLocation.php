<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: course_group_right_location.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * 
 * @package rights.lib
 * @author Hans de Bisschop
 */
class CourseGroupRightLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_RIGHT_ID = 'right_id';
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_COURSE_GROUP_ID = 'course_group_id';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties of all users.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_RIGHT_ID, 
                self :: PROPERTY_COURSE_GROUP_ID, 
                self :: PROPERTY_LOCATION_ID, 
                self :: PROPERTY_VALUE));
    }

    public function get_right_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_ID);
    }

    public function set_right_id($right_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_ID, $right_id);
    }

    public function get_course_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_GROUP_ID);
    }

    public function set_course_group_id($course_group_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_GROUP_ID, $course_group_id);
    }

    public function get_location_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
    }

    public function set_location_id($location_id)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
    }

    public function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    public function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    public function invert()
    {
        $value = $this->get_value();
        $this->set_value(! $value);
    }

    public function is_enabled()
    {
        return $this->get_value() == true;
    }
}
