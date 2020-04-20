<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseToolRelCourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.course_sections
 */
class CourseSectionToolSelectorForm extends FormValidator
{

    private $course_section;

    public function __construct($course_section, $action)
    {
        parent::__construct('course_sections', self::FORM_METHOD_POST, $action);

        $this->course_section = $course_section;
        $this->build_basic_form();
        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $tools = $this->get_tools();

        // $sel = &
        $this->addElement(
            'select',
            'tools',
            Translation::get('SelectTools'),
            $tools,
            array('multiple' => 'true', 'size' => (count($tools) > 10 ? 10 : count($tools))));

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Retrieve the tools the user can select (active and type tool)
     *
     * @return array
     */
    public function get_tools()
    {

        // retrieve the tools
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseTool::class_name(), CourseTool::PROPERTY_SECTION_TYPE),
            new StaticConditionVariable(CourseSection::TYPE_TOOL));

        $tools = DataManager::retrieves(CourseTool::class_name(), new DataClassRetrievesParameters($condition));

        $active_tools = array();

        while ($tool = $tools->next_result())
        {
            $course_settings_controller = CourseSettingsController::getInstance();
            $course = DataManager::retrieve_by_id(Course::class_name(), Request::get('course'));

            if ($course_settings_controller->get_course_setting(
                $course,
                CourseSetting::COURSE_SETTING_TOOL_ACTIVE,
                $tool->get_id()))
            {
                $active_tools[$tool->get_id()] = $tool->get_name();
            }
        }

        return $active_tools;
    }

    /**
     * Retrieve the tools already registered
     *
     * @return type
     */
    public function get_registered_tools()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                CourseToolRelCourseSection::class_name(),
                CourseToolRelCourseSection::PROPERTY_SECTION_ID),
            new StaticConditionVariable($this->course_section->get_id()));

        return $registered_tools_resultset = DataManager::retrieves(
            CourseToolRelCourseSection::class_name(),
            new DataClassRetrievesParameters($condition));
    }

    public function update_course_modules()
    {
        // $course_section = $this->course_section;
        $values = $this->exportValues();
        $selected_tools = $values['tools'];

        // retrieve the sections for this course
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class_name(), CourseSection::PROPERTY_COURSE_ID),
            new StaticConditionVariable(Request::get('course')));
        $course_sections = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            CourseSection::class_name(),
            new DataClassRetrievesParameters($condition));

        $course_section_ids = array();
        while ($course_section = $course_sections->next_result())
        {
            $course_section_ids[] = $course_section->get_id();
        }

        $registered_tools = $this->get_registered_tools();
        while ($registered_tool = $registered_tools->next_result())
        {
            if (in_array($registered_tool->get_tool_id(), $selected_tools))
            {
                // remove from selected tools because it's already assigned to
                // this course section
                $selected_tools = array_diff($selected_tools, array($registered_tool->get_tool_id()));
            }
            else
            {
                // remove the registerd tool from the course section (as it is
                // no longer selected)
                $registered_tool->delete();
            }
        }

        // add the remaining selected tools to the course section
        foreach ($selected_tools as $selected_tool_id)
        {
            // retrieve the relation if it exists for this tool (in another
            // section), so we can update it to the new tool
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseToolRelCourseSection::class_name(),
                    CourseToolRelCourseSection::PROPERTY_TOOL_ID),
                new StaticConditionVariable($selected_tool_id));
            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    CourseToolRelCourseSection::class_name(),
                    CourseToolRelCourseSection::PROPERTY_SECTION_ID),
                $course_section_ids);
            $condition = new AndCondition($conditions);

            $course_tool_rel_course_sections = DataManager::retrieves(
                CourseToolRelCourseSection::class_name(),
                new DataClassRetrievesParameters($condition));

            if ($course_tool_rel_course_sections->size() > 0)
            {
                $course_tool_rel_course_section = $course_tool_rel_course_sections->next_result();
                $course_tool_rel_course_section->set_section_id($this->course_section->get_id());
                if (! $course_tool_rel_course_section->update())
                {
                    return false;
                }
            }
            else
            {
                $course_tool_rel_course_section = new CourseToolRelCourseSection();
                $course_tool_rel_course_section->set_tool_id($selected_tool_id);
                $course_tool_rel_course_section->set_section_id($this->course_section->get_id());
                if (! $course_tool_rel_course_section->create())
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Sets default values.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $registered_tools = $this->get_registered_tools();
        $registered_tools_array = array();
        while ($registered_tool = $registered_tools->next_result())
        {
            $registered_tools_array[] = $registered_tool->get_tool_id();
        }
        $defaults['tools'] = $registered_tools_array;

        parent::setDefaults($defaults);
    }
}
