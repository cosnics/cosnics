<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class defines a setting for a course.
 * The course settings are defined in xml files in the weblcms and the
 * different tools. The settings can be parsed with the CourseSettingsParser
 * class.
 * 
 * @package application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSetting extends DataClass
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_TOOL_ID = 'tool_id';
    const PROPERTY_GLOBAL_SETTING = 'global_setting';
    const PROPERTY_NAME = 'name';
    
    /**
     * **************************************************************************************************************
     * Foreign properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_TOOL = 'tool';
    
    /**
     * **************************************************************************************************************
     * Additional properties *
     * **************************************************************************************************************
     */
    
    /**
     * Property only used when joining with the course tool table
     */
    const PROPERTY_COURSE_TOOL_NAME = 'course_tool_name';
    
    /**
     * **************************************************************************************************************
     * Definition of static repeatable settings *
     * **************************************************************************************************************
     */
    const COURSE_SETTING_TOOL_ACTIVE = 'active';
    const COURSE_SETTING_TOOL_VISIBLE = 'visible';

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
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_TOOL_ID;
        $extended_property_names[] = self::PROPERTY_GLOBAL_SETTING;
        $extended_property_names[] = self::PROPERTY_NAME;
        
        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Additional Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the static repeatable settings for tools
     * 
     * @return String[]
     */
    static function get_static_tool_settings()
    {
        return array(self::COURSE_SETTING_TOOL_ACTIVE, self::COURSE_SETTING_TOOL_VISIBLE);
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the tool_id property of this object
     * 
     * @return String
     */
    function get_tool_id()
    {
        return $this->get_default_property(self::PROPERTY_TOOL_ID);
    }

    /**
     * Sets the tool_id property of this object
     * 
     * @param $tool_id String
     */
    function set_tool_id($tool_id)
    {
        $this->set_default_property(self::PROPERTY_TOOL_ID, $tool_id);
    }

    /**
     * Returns the global_setting property of this object
     * 
     * @return String
     */
    function is_global_setting()
    {
        return $this->get_default_property(self::PROPERTY_GLOBAL_SETTING);
    }

    /**
     * Sets the global_setting property of this object
     * 
     * @param $global_setting String
     */
    function set_global_setting($global_setting)
    {
        $this->set_default_property(self::PROPERTY_GLOBAL_SETTING, $global_setting);
    }

    /**
     * Returns the name property of this object
     * 
     * @return String
     */
    function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name property of this object
     * 
     * @param $name String
     */
    function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * **************************************************************************************************************
     * Foreign Properties Setters / Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the name for the course tool foreign object
     * 
     * @return String
     */
    public function get_course_tool_name()
    {
        $course_tool_name = $this->get_optional_property(self::PROPERTY_COURSE_TOOL_NAME);
        if (! $course_tool_name)
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
     * **************************************************************************************************************
     * Additional Properties Setters / Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the course tool of this course setting object (lazy loading)
     * 
     * @return \application\weblcms\CourseTool
     */
    public function get_course_tool()
    {
        return $this->get_foreign_property(
            self::FOREIGN_PROPERTY_TOOL, 
            \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool::class_name());
    }

    /**
     * Sets the course tool of this course setting object
     * 
     * @param $course_tool \application\weblcms\CourseTool
     */
    public function set_course_tool(\Chamilo\Application\Weblcms\Storage\DataClass\CourseTool $course_tool)
    {
        $this->set_foreign_property(self::FOREIGN_PROPERTY_TOOL, $course_tool);
    }
}

?>
