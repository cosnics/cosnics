<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package application.lib.weblcms.course
 */

/**
 * This class describes a CourseModule data object
 *
 * @author Hans De Bisschop
 */
class CourseModule extends DataClass
{

    /**
     * CourseModule properties
     */
    const PROPERTY_COURSE_CODE = 'course_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_VISIBLE = 'visible';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_SORT = 'sort';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_COURSE_CODE,
                self::PROPERTY_NAME,
                self::PROPERTY_VISIBLE,
                self::PROPERTY_SECTION,
                self::PROPERTY_SORT));
    }

    /**
     * Returns the course_code of this CourseModule.
     *
     * @return the course_code.
     */
    public function get_course_code()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_CODE);
    }

    /**
     * Sets the course_code of this CourseModule.
     *
     * @param course_code
     */
    public function set_course_code($course_code)
    {
        $this->set_default_property(self::PROPERTY_COURSE_CODE, $course_code);
    }

    /**
     * Returns the name of this CourseModule.
     *
     * @return the name.
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name of this CourseModule.
     *
     * @param name
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the visible of this CourseModule.
     *
     * @return the visible.
     */
    public function get_visible()
    {
        return $this->get_default_property(self::PROPERTY_VISIBLE);
    }

    /**
     * Sets the visible of this CourseModule.
     *
     * @param visible
     */
    public function set_visible($visible)
    {
        $this->set_default_property(self::PROPERTY_VISIBLE, $visible);
    }

    /**
     * Returns the section of this CourseModule.
     *
     * @return the section.
     */
    public function get_section()
    {
        return $this->get_default_property(self::PROPERTY_SECTION);
    }

    /**
     * Sets the section of this CourseModule.
     *
     * @param section
     */
    public function set_section($section)
    {
        $this->set_default_property(self::PROPERTY_SECTION, $section);
    }

    /**
     * Returns the sort of this CourseModule.
     *
     * @return the sort.
     */
    public function get_sort()
    {
        return $this->get_default_property(self::PROPERTY_SORT);
    }

    /**
     * Sets the sort of this CourseModule.
     *
     * @param sort
     */
    public function set_sort($sort)
    {
        $this->set_default_property(self::PROPERTY_SORT, $sort);
    }

    public static function convert_tools($tools, $course_code = null, $course_type_tools = false, $form = null)
    {
        $tools_array = array();

        foreach ($tools as $index => $tool)
        {
            $tool_visible = 1;
            if ($course_type_tools)
            {
                $tool_visible = $tool->get_visible_default();
                $tool = $tool->get_name();
            }

            $element_default = $tool . "elementdefaulte";
            $course_module = new CourseModule();
            $course_module->set_course_code($course_code);
            $course_module->set_name($tool);
            $course_module->set_visible(
                (! is_null($form) ? $form->parse_checkbox_value($form->getSubmitValue($element_default)) : $tool_visible));
            $course_module->set_section("basic");
            $course_module->set_sort($index);
            $tools_array[] = $course_module;
        }
        return $tools_array;
    }

    public function create($create_in_batch = false)
    {
        $succes = parent::create();
        if (! $succes)
        {
            return false;
        }

        return WeblcmsRights::getInstance()->create_location_in_courses_subtree(
            WeblcmsRights::TYPE_COURSE_MODULE,
            $this->get_id(),
            WeblcmsRights::getInstance()->get_courses_subtree_root_id($this->get_course_code()),
            $this->get_course_code(),
            $create_in_batch);
    }

    public function delete()
    {
        $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_COURSE_MODULE,
            $this->get_id(),
            $this->get_course_code());
        if ($location)
        {
            if (! $location->delete())
            {
                return false;
            }
        }
        return parent::delete();
    }
}
