<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package application.lib.weblcms.course
 */

/**
 * This class describes a CourseModuleLastAccess data object
 *
 * @author Hans De Bisschop
 */
class CourseModuleLastAccess extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ACCESS_DATE = 'access_date';
    public const PROPERTY_CATEGORY_ID = 'category_id';
    public const PROPERTY_COURSE_CODE = 'course_id';
    public const PROPERTY_MODULE_NAME = 'module_name';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * Get the default properties
     *
     * @param string[] $extendedPropertyNames
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_COURSE_CODE,
                self::PROPERTY_USER_ID,
                self::PROPERTY_MODULE_NAME,
                self::PROPERTY_CATEGORY_ID,
                self::PROPERTY_ACCESS_DATE
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_module_last_access';
    }

    /**
     * Returns the access_date of this CourseModuleLastAccess.
     *
     * @return the access_date.
     */
    public function get_access_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_ACCESS_DATE);
    }

    /**
     * Returns the category_id of this CourseModuleLastAccess.
     *
     * @return the category_id.
     */
    public function get_category_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CATEGORY_ID);
    }

    /**
     * Returns the course_code of this CourseModuleLastAccess.
     *
     * @return the course_code.
     */
    public function get_course_code()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_CODE);
    }

    /**
     * Returns the module_name of this CourseModuleLastAccess.
     *
     * @return the module_name.
     */
    public function get_module_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODULE_NAME);
    }

    /**
     * Returns the user_id of this CourseModuleLastAccess.
     *
     * @return the user_id.
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * Sets the access_date of this CourseModuleLastAccess.
     *
     * @param access_date
     */
    public function set_access_date($access_date)
    {
        $this->setDefaultProperty(self::PROPERTY_ACCESS_DATE, $access_date);
    }

    /**
     * Sets the category_id of this CourseModuleLastAccess.
     *
     * @param category_id
     */
    public function set_category_id($category_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CATEGORY_ID, $category_id);
    }

    /**
     * Sets the course_code of this CourseModuleLastAccess.
     *
     * @param course_code
     */
    public function set_course_code($course_code)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_CODE, $course_code);
    }

    /**
     * Sets the module_name of this CourseModuleLastAccess.
     *
     * @param module_name
     */
    public function set_module_name($module_name)
    {
        $this->setDefaultProperty(self::PROPERTY_MODULE_NAME, $module_name);
    }

    /**
     * Sets the user_id of this CourseModuleLastAccess.
     *
     * @param user_id
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
