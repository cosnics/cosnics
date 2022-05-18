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
    const PROPERTY_COURSE_CODE = 'course_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_VISIBLE = 'visible';

    public static function convert_tools($tools, $course_code = null, $course_type_tools = false, $form = null)
    {
        $tools_array = [];

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
                (!is_null($form) ? $form->parse_checkbox_value($form->getSubmitValue($element_default)) : $tool_visible)
            );
            $course_module->set_section("basic");
            $course_module->set_sort($index);
            $tools_array[] = $course_module;
        }

        return $tools_array;
    }

    public function create($create_in_batch = false)
    {
        $succes = parent::create();
        if (!$succes)
        {
            return false;
        }

        return WeblcmsRights::getInstance()->create_location_in_courses_subtree(
            WeblcmsRights::TYPE_COURSE_MODULE, $this->get_id(),
            WeblcmsRights::getInstance()->get_courses_subtree_root_id($this->get_course_code()),
            $this->get_course_code(), $create_in_batch
        );
    }

    public function delete()
    {
        $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
            WeblcmsRights::TYPE_COURSE_MODULE, $this->get_id(), $this->get_course_code()
        );
        if ($location)
        {
            if (!$location->delete())
            {
                return false;
            }
        }

        return parent::delete();
    }

    /**
     * Returns the course_code of this CourseModule.
     *
     * @return the course_code.
     */
    public function get_course_code()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_CODE);
    }

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_COURSE_CODE,
                self::PROPERTY_NAME,
                self::PROPERTY_VISIBLE,
                self::PROPERTY_SECTION,
                self::PROPERTY_SORT
            )
        );
    }

    /**
     * Returns the name of this CourseModule.
     *
     * @return the name.
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * Returns the section of this CourseModule.
     *
     * @return the section.
     */
    public function get_section()
    {
        return $this->getDefaultProperty(self::PROPERTY_SECTION);
    }

    /**
     * Returns the sort of this CourseModule.
     *
     * @return the sort.
     */
    public function get_sort()
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_course_module';
    }

    /**
     * Returns the visible of this CourseModule.
     *
     * @return the visible.
     */
    public function get_visible()
    {
        return $this->getDefaultProperty(self::PROPERTY_VISIBLE);
    }

    /**
     * Sets the course_code of this CourseModule.
     *
     * @param course_code
     */
    public function set_course_code($course_code)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_CODE, $course_code);
    }

    /**
     * Sets the name of this CourseModule.
     *
     * @param name
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the section of this CourseModule.
     *
     * @param section
     */
    public function set_section($section)
    {
        $this->setDefaultProperty(self::PROPERTY_SECTION, $section);
    }

    /**
     * Sets the sort of this CourseModule.
     *
     * @param sort
     */
    public function set_sort($sort)
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }

    /**
     * Sets the visible of this CourseModule.
     *
     * @param visible
     */
    public function set_visible($visible)
    {
        $this->setDefaultProperty(self::PROPERTY_VISIBLE, $visible);
    }
}
