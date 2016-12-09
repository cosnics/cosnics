<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Relation class that links a tool to a section
 * 
 * @author Pieterjan Broekaert Hogeschool Gent
 */
class CourseToolRelCourseSection extends DataClass
{
    
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_TOOL_ID = 'tool_id';
    const PROPERTY_SECTION_ID = 'section_id';

    /**
     * Get the default properties.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_TOOL_ID, self::PROPERTY_SECTION_ID));
    }

    /**
     * Returns the tool_id property of this object
     * 
     * @return integer
     */
    public function get_tool_id()
    {
        return $this->get_default_property(self::PROPERTY_TOOL_ID);
    }

    /**
     * Sets the course_id property of this object
     * 
     * @param $course_id String
     */
    public function set_tool_id($tool_id)
    {
        $this->set_default_property(self::PROPERTY_TOOL_ID, $tool_id);
    }

    /**
     * Returns the section_id property of this object
     * 
     * @return integer
     */
    public function get_section_id()
    {
        return $this->get_default_property(self::PROPERTY_SECTION_ID);
    }

    /**
     * Sets the course_id property of this object
     * 
     * @param $course_id String
     */
    public function set_section_id($section_id)
    {
        $this->set_default_property(self::PROPERTY_SECTION_ID, $section_id);
    }
}
