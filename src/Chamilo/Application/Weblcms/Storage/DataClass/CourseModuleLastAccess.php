<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: course_module_last_access.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.course
 */

/**
 * This class describes a CourseModuleLastAccess data object
 * 
 * @author Hans De Bisschop
 */
class CourseModuleLastAccess extends DataClass
{
    
    /**
     * CourseModuleLastAccess properties
     */
    const PROPERTY_COURSE_CODE = 'course_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_MODULE_NAME = 'module_name';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_ACCESS_DATE = 'access_date';

    /**
     * Get the default properties
     * 
     * @param string[] $extended_property_names
     *
     * @return string[] The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_COURSE_CODE, 
                self::PROPERTY_USER_ID, 
                self::PROPERTY_MODULE_NAME, 
                self::PROPERTY_CATEGORY_ID, 
                self::PROPERTY_ACCESS_DATE));
    }

    /**
     * Returns the course_code of this CourseModuleLastAccess.
     * 
     * @return the course_code.
     */
    public function get_course_code()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_CODE);
    }

    /**
     * Sets the course_code of this CourseModuleLastAccess.
     * 
     * @param course_code
     */
    public function set_course_code($course_code)
    {
        $this->set_default_property(self::PROPERTY_COURSE_CODE, $course_code);
    }

    /**
     * Returns the user_id of this CourseModuleLastAccess.
     * 
     * @return the user_id.
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id of this CourseModuleLastAccess.
     * 
     * @param user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the module_name of this CourseModuleLastAccess.
     * 
     * @return the module_name.
     */
    public function get_module_name()
    {
        return $this->get_default_property(self::PROPERTY_MODULE_NAME);
    }

    /**
     * Sets the module_name of this CourseModuleLastAccess.
     * 
     * @param module_name
     */
    public function set_module_name($module_name)
    {
        $this->set_default_property(self::PROPERTY_MODULE_NAME, $module_name);
    }

    /**
     * Returns the category_id of this CourseModuleLastAccess.
     * 
     * @return the category_id.
     */
    public function get_category_id()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY_ID);
    }

    /**
     * Sets the category_id of this CourseModuleLastAccess.
     * 
     * @param category_id
     */
    public function set_category_id($category_id)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY_ID, $category_id);
    }

    /**
     * Returns the access_date of this CourseModuleLastAccess.
     * 
     * @return the access_date.
     */
    public function get_access_date()
    {
        return $this->get_default_property(self::PROPERTY_ACCESS_DATE);
    }

    /**
     * Sets the access_date of this CourseModuleLastAccess.
     * 
     * @param access_date
     */
    public function set_access_date($access_date)
    {
        $this->set_default_property(self::PROPERTY_ACCESS_DATE, $access_date);
    }
}
