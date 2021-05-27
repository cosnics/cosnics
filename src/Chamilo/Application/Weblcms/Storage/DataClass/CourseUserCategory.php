<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package application.lib.weblcms.course
 */

/**
 * This class represents a course user category in the weblcms.
 * course user categories have a number of default
 * properties: - id: the numeric course user category ID; - user: the course user category user; - sort: the course user
 * category sort order; - title: the course user category title; To access the values of the properties, this class and
 * its subclasses should provide accessor methods. The names of the properties should be defined as class constants, for
 * standardization purposes. It is recommended that the names of these constants start with the string "PROPERTY_".
 */
class CourseUserCategory extends DataClass
{
    const PROPERTY_TITLE = 'title';

    /**
     * Get the default properties of all user course user categories.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(array(self::PROPERTY_TITLE));
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'weblcms_course_user_category';
    }

    /**
     * Returns the title of this course user category object
     *
     * @return string
     */
    public function get_title()
    {
        return $this->get_default_property(self::PROPERTY_TITLE);
    }

    /**
     * Sets the title of this course user category object
     *
     * @param $title string
     */
    public function set_title($title)
    {
        $this->set_default_property(self::PROPERTY_TITLE, $title);
    }
}
