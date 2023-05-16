<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class defines a setting for a course.
 * The course settings are defined in xml files in the weblcms and the
 * different tools. The settings can be parsed with the CourseSettingsParser
 * class.
 *
 * @package application\weblcms;
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class CourseSetting extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const COURSE_SETTING_TOOL_ACTIVE = 'active';
    public const COURSE_SETTING_TOOL_VISIBLE = 'visible';

    public const FOREIGN_PROPERTY_TOOL = 'tool';

    public const PROPERTY_COURSE_TOOL_NAME = 'course_tool_name';
    public const PROPERTY_GLOBAL_SETTING = 'global_setting';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_TOOL_ID = 'tool_id';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_TOOL_ID;
        $extendedPropertyNames[] = self::PROPERTY_GLOBAL_SETTING;
        $extendedPropertyNames[] = self::PROPERTY_NAME;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * **************************************************************************************************************
     * Additional Functionality *
     * **************************************************************************************************************
     */

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_course_setting';
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the course tool of this course setting object (lazy loading)
     *
     * @return \application\weblcms\CourseTool
     */
    public function get_course_tool()
    {
        return $this->getForeignProperty(
            self::FOREIGN_PROPERTY_TOOL, CourseTool::class
        );
    }

    /**
     * Returns the name for the course tool foreign object
     *
     * @return String
     */
    public function get_course_tool_name()
    {
        $course_tool_name = $this->getOptionalProperty(self::PROPERTY_COURSE_TOOL_NAME);
        if (!$course_tool_name)
        {
            $course_tool = $this->get_course_tool();
            if ($course_tool)
            {
                $course_tool_name = $course_tool->get_name();
            }
        }

        return $course_tool_name;
    }

    /**
     * Returns the name property of this object
     *
     * @return String
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * Returns the static repeatable settings for tools
     *
     * @return String[]
     */
    public static function get_static_tool_settings()
    {
        return [self::COURSE_SETTING_TOOL_ACTIVE, self::COURSE_SETTING_TOOL_VISIBLE];
    }

    /**
     * Returns the tool_id property of this object
     *
     * @return String
     */
    public function get_tool_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_TOOL_ID);
    }

    /**
     * Returns the global_setting property of this object
     *
     * @return String
     */
    public function is_global_setting()
    {
        return $this->getDefaultProperty(self::PROPERTY_GLOBAL_SETTING);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * Sets the course tool of this course setting object
     *
     * @param $course_tool \application\weblcms\CourseTool
     */
    public function set_course_tool(CourseTool $course_tool)
    {
        $this->setForeignProperty(self::FOREIGN_PROPERTY_TOOL, $course_tool);
    }

    /**
     * **************************************************************************************************************
     * Additional Properties Setters / Getters *
     * **************************************************************************************************************
     */

    /**
     * Sets the global_setting property of this object
     *
     * @param $global_setting String
     */
    public function set_global_setting($global_setting)
    {
        $this->setDefaultProperty(self::PROPERTY_GLOBAL_SETTING, $global_setting);
    }

    /**
     * Sets the name property of this object
     *
     * @param $name String
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the tool_id property of this object
     *
     * @param $tool_id String
     */
    public function set_tool_id($tool_id)
    {
        $this->setDefaultProperty(self::PROPERTY_TOOL_ID, $tool_id);
    }
}
