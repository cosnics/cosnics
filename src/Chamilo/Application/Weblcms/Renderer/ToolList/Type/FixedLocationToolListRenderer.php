<?php

namespace Chamilo\Application\Weblcms\Renderer\ToolList\Type;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseToolRelCourseSection;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.tool_list_renderer
 */

/**
 * Tool list renderer which displays all course tools on a fixed location.
 * Disabled tools will be shown in a disabled
 * looking way.
 */
class FixedLocationToolListRenderer extends ToolListRenderer
{
    public const PARAM_SELECTED_TAB = 'section';

    /**
     * The Course
     *
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    private $course;

    /**
     * Determine if the current user is a teacher
     *
     * @var bool
     */
    private $is_course_admin;

    /**
     * The available number of columns
     *
     * @var int
     */
    private $number_of_columns = 2;

    /**
     * Constructor
     *
     * @param WebLcms $parent The parent application
     */
    public function __construct($parent, $visible_tools)
    {
        parent::__construct($parent, $visible_tools);

        $course = $parent->get_course();
        $this->course = $course;

        $course_settings_controller = CourseSettingsController::getInstance();
        $course_tool_layout = $course_settings_controller->get_course_setting(
            $this->course, CourseSettingsConnector::TOOL_LAYOUT
        );

        $this->number_of_columns = ($course_tool_layout % 2 == 0) ? 3 : 2;

        $this->is_course_admin = $this->get_parent()->get_parent()->is_teacher();
    }

    // Inherited

    public function display_block_footer($section)
    {
        $html = [];

        $html[] = '<div class="clearfix"></div>';

        if ($section->getType() == CourseSection::TYPE_TOOL || $section->getType() == CourseSection::TYPE_DISABLED)
        {
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function display_block_header($section, $block_name)
    {
        $html = [];

        if ($section->getType() == CourseSection::TYPE_TOOL)
        {
            $html[] = '<div class="toolblock" id="block_' . $section->get_id() . '" style="width:100%;">';
        }

        if ($section->getType() == CourseSection::TYPE_DISABLED)
        {
            $html[] = '<div class="disabledblock" id="block_' . $section->get_id() . '" style="width:100%;">';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the course section for a given tool object
     *
     * @param CourseTool $tool
     * @param int[int] $section_types_map
     *
     * @return int (the id of the course section)
     */
    protected function get_course_section_for_tool(CourseTool $tool, $section_types_map)
    {
        $course_settings_controller = CourseSettingsController::getInstance();
        $course_tool_layout = $course_settings_controller->get_course_setting(
            $this->course, CourseSettingsConnector::TOOL_LAYOUT
        );

        if ($course_tool_layout > 2)
        {
            $tool_visible = $course_settings_controller->get_course_setting(
                $this->course, CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool->get_id()
            );

            if (!$tool_visible && $tool->get_section_type() != CourseSection::TYPE_ADMIN)
            {
                return $section_types_map[CourseSection::TYPE_DISABLED];
            }
        }

        if ($tool->get_section_type() != CourseSection::TYPE_TOOL)
        {
            return $section_types_map[$tool->get_section_type()];
        }

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                CourseToolRelCourseSection::class, CourseToolRelCourseSection::PROPERTY_TOOL_ID
            ), new StaticConditionVariable($tool->get_id())
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                CourseToolRelCourseSection::class, CourseToolRelCourseSection::PROPERTY_SECTION_ID
            ), $section_types_map
        );

        $condition = new AndCondition($conditions);

        $course_tool_rel_course_section = DataManager::retrieves(
            CourseToolRelCourseSection::class, new DataClassRetrievesParameters($condition)
        );

        if ($course_tool_rel_course_section->count() > 0)
        {
            return $course_tool_rel_course_section->current()->get_section_id();
        }

        return $section_types_map[CourseSection::TYPE_TOOL];
    }

    private function get_publication_links()
    {
        if (!isset($this->publication_links))
        {
            $parent = $this->get_parent();

            $conditions = [];
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($parent->get_course_id())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_SHOW_ON_HOMEPAGE
                ), new StaticConditionVariable(1)
            );
            $condition = new AndCondition($conditions);

            $this->publication_links = DataManager::retrieves(
                ContentObjectPublication::class, new DataClassRetrievesParameters($condition)
            );
        }

        return $this->publication_links;
    }

    /**
     * Show the links to publications in this course
     */
    private function show_links($section)
    {
        $parent = $this->get_parent();
        $publications = $this->get_publication_links();

        if ($publications->count() == 0)
        {
            return '<div class="alert alert-info">' . Translation::get('NoLinksAvailable') . '</div>';
        }

        $columnClass = $this->number_of_columns == 2 ? 'col-md-6 col-sm-12' : 'col-md-4 col-sm-12';

        $count = 0;

        $html = [];

        if ($count % $this->number_of_columns == 0)
        {
            $html[] = '<div class="row">';
        }

        foreach ($publications as $publication)
        {
            if ($count > 0 && $count % $this->number_of_columns == 0)
            {
                $html[] = '</div>';
                $html[] = '<div class="row">';
            }

            $html[] = '<div class="' . $columnClass . ' tool-link">';

            if ($publication->is_hidden() == 0)
            {
                $lcms_action = \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::ACTION_HIDE_PUBLICATION;
                $visibleClass = 'eye-open';
                $glyph = new FontAwesomeGlyph('eye', [], null, 'fas');
                $isDisabled = false;
                $link_class = '';
            }
            else
            {
                $lcms_action = \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::ACTION_SHOW_PUBLICATION;
                $glyph = new FontAwesomeGlyph('eye-slash', array('text-muted'), null, 'fas');
                $isDisabled = true;
                $link_class = ' class="text-muted"';
            }

            $title = htmlspecialchars($publication->get_content_object()->get_title());

            if ($parent->is_allowed(WeblcmsRights::EDIT_RIGHT) || $publication->is_visible_for_target_users())
            {
                $html[] = '<div class="tool-link-actions">';

                // Show visibility-icon
                if ($parent->is_allowed(WeblcmsRights::EDIT_RIGHT))
                {
                    $html[] = '<a href="' . $parent->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => $lcms_action,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->get_id(
                                )
                            )
                        ) . '">' . $glyph->render() . '</a>';
                }

                // Show delete-icon
                if ($parent->is_allowed(WeblcmsRights::DELETE_RIGHT))
                {
                    $glyph = new FontAwesomeGlyph('times', array('text-danger'), null, 'fas');

                    $html[] = '<a href="' . $parent->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::ACTION_DELETE_LINKS,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->get_id(
                                )
                            )
                        ) . '">' . $glyph->render() . '</a>';
                }

                $html[] = '</div>';
                // Show tool-icon + name

                if ($publication->get_tool() ==
                    \Chamilo\Application\Weblcms\Tool\Implementation\Link\Manager::TOOL_NAME)
                {
                    $url = $publication->get_content_object()->get_url();
                    $target = ' target="_blank"';
                }
                else
                {
                    $class = 'Chamilo\Application\Weblcms\Tool\Implementation\\' .
                        StringUtilities::getInstance()->createString($publication->get_tool())->upperCamelize() .
                        '\Manager';
                    $url = $parent->get_url(
                        array(
                            'tool_action' => null,
                            Manager::PARAM_COMPONENT_ACTION => null,
                            Manager::PARAM_TOOL => $publication->get_tool(),
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => $class::ACTION_VIEW,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->get_id()
                        ), [], true
                    );
                    $target = '';
                }

                $toolNamespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace(
                    $publication->get_tool()
                );

                $identGlyph = new NamespaceIdentGlyph(
                    $toolNamespace, true, false, $isDisabled, IdentGlyph::SIZE_MEDIUM, array('fa-fw')
                );

                $html[] = $identGlyph->render();
                $html[] = '&nbsp;';
                $html[] = '<a href="' . $url . '"' . $target . $link_class . '>';
                $html[] = $title;
                $html[] = '</a>';

                $count ++;
            }

            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    private function show_section_tools($section, $tools)
    {
        if (count($tools) == 0)
        {
            return '<div class="alert alert-info">' . Translation::get('NoToolsAvailable') . '</div>';
        }

        $parent = $this->get_parent();

        $columnClass = $this->number_of_columns == 2 ? 'col-md-6 col-sm-12' : 'col-md-4 col-sm-12';

        $count = 0;

        $html = [];

        $course_settings_controller = CourseSettingsController::getInstance();

        $html[] = '<div class="row">';

        foreach ($tools as $tool)
        {
            $html[] = '<div class="' . $columnClass . '">';

            $tool_namespace = $tool->getContext();

            $tool_visible = $course_settings_controller->get_course_setting(
                $this->course, CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool->get_id()
            );

            $isNew = false;
            $isDisabled = false;

            if ($tool_visible || $section->getType() == CourseSection::TYPE_ADMIN)
            {
                $lcms_action =
                    \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::ACTION_MAKE_TOOL_INVISIBLE;
                $glyph = new FontAwesomeGlyph('eye', [], null, 'fas');

                if ($parent->tool_has_new_publications($tool->get_name(), $this->course))
                {
                    $isNew = true;
                }

                $link_class = '';
            }
            else
            {
                $lcms_action = \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::ACTION_MAKE_TOOL_VISIBLE;
                $glyph = new FontAwesomeGlyph('eye-slash', array('text-muted'), null, 'fas');
                $isDisabled = true;
                $link_class = ' class="invisible-tool"';
            }

            $title = Translation::get('TypeName', null, $tool_namespace);

            if ($section->getType() == CourseSection::TYPE_TOOL || $section->getType() == CourseSection::TYPE_DISABLED)
            {
                $html[] = '<div id="tool_' . $tool->get_id() . '" class="tool" style="display: block !important">';
                $id = ' id="drag_' . $tool->get_id() . '"';
            }
            else
            {
                $html[] = '<div class="tool">';
            }

            $canChangeCourseSetting = $course_settings_controller->canChangeCourseSetting(
                $this->course, CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool->get_id()
            );

            // Show visibility-icon
            if ($this->is_course_admin && $canChangeCourseSetting && $section->getType() != CourseSection::TYPE_ADMIN)
            {
                $html[] = '<a href="' . $parent->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::PARAM_ACTION => $lcms_action,
                            \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::PARAM_TOOL => $tool->get_name(
                            )
                        )
                    ) . '">' . $glyph->render() . '</a>';
                $html[] = '&nbsp;&nbsp;&nbsp;';
            }
            else
            {
                $html[] = '<span style="width: 27px; display: inline-block;" class="no-visibility-icon"></span>';
            }

            $identGlyph = new NamespaceIdentGlyph(
                $tool_namespace, true, $isNew, $isDisabled, IdentGlyph::SIZE_MEDIUM, array('fa-fw')
            );
            $html[] = $identGlyph->render();

            $html[] = '&nbsp;';

            $html[] = '<a id="tool_text" href="' . $parent->get_url(
                    array(Manager::PARAM_TOOL => $tool->get_name()), array(
                    Manager::PARAM_COMPONENT_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE,
                    Manager::PARAM_CATEGORY
                ), true
                ) . '" ' . $link_class . '>';

            $html[] = $title;
            $html[] = '</a>';

            $html[] = '<div class="clearfix"></div>';

            $html[] = '</div>';
            $html[] = '</div>';

            $count ++;
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function toHtml()
    {
        $tools = [];
        $html = [];

        $course_settings_controller = CourseSettingsController::getInstance();
        $course_tool_layout = $course_settings_controller->get_course_setting(
            $this->course, CourseSettingsConnector::TOOL_LAYOUT
        );

        $visible_tools = $this->get_visible_tools();

        $section_types_map = [];

        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->course->get_id())
        );
        $order_property = array(
            new OrderProperty(
                new PropertyConditionVariable(CourseSection::class, CourseSection::PROPERTY_DISPLAY_ORDER)
            )
        );
        $parameters = new DataClassRetrievesParameters($condition, null, null, new OrderBy($order_property));
        $sections = DataManager::retrieves(CourseSection::class, $parameters);

        foreach ($sections as $section)
        {
            $section_types_map[$section->getType()] = $section->get_id();
        }

        $sorted_tools = [];

        foreach ($visible_tools as $tool)
        {
            $tool_namespace = $tool->getContext();

            $sorted_tools[Translation::get('TypeName', null, $tool_namespace)] = $tool;
        }

        ksort($sorted_tools);

        foreach ($sorted_tools as $tool)
        {
            if ($tool->get_name() == 'home')
            {
                continue;
            }

            $tools[$this->get_course_section_for_tool($tool, $section_types_map)][] = $tool;
        }

        $html[] = '<div id="coursecode" style="display: none;">' . $this->course->get_id() . '</div>';

        $sections->first();

        if ($sections->count() == 0)
        {
            return '<div class="alert alert-warning">' . Translation::get('NoVisibleCourseSections') . '</div>';
        }

        $html[] = '<ul class="nav nav-tabs tool-list-tabs">';

        foreach ($sections as $section)
        {
            $sec_name = $section->getType() == CourseSection::TYPE_CUSTOM ? $section->get_name() : Translation::get(
                $section->get_name()
            );

            if (!$section->is_visible())
            {
                if (!$this->is_course_admin)
                {
                    continue;
                }
                else
                {
                    $name = '<span style="color: gray">' . $sec_name . '</span>';
                    $section->set_name($name);
                }
            }

            if ($section->getType() == CourseSection::TYPE_DISABLED &&
                ($course_tool_layout < 3 || !$this->is_course_admin))
            {
                continue;
            }

            if ($section->getType() == CourseSection::TYPE_LINK)
            {
                $publications = $this->get_publication_links();

                if ($publications->count() == 0)
                {
                    continue;
                }
            }

            if ($section->getType() == CourseSection::TYPE_ADMIN && !$this->is_course_admin)
            {
                continue;
            }

            $selectedTab = $this->get_parent()->getRequest()->getFromRequestOrQuery(self::PARAM_SELECTED_TAB);

            if ((isset($selectedTab) && $section->getId() == $selectedTab) ||
                (!isset($selectedTab) && $sections->key() === 0))
            {
                $active = 'active';
            }
            else
            {
                $active = '';
            }

            $url = $this->get_parent()->get_url(array(self::PARAM_SELECTED_TAB => $section->get_id()));

            $html[] = '<li role="presentation" class="' . $active . '"><a href="';
            $html[] = $url;
            $html[] = '">' . $sec_name . '</a></li>';

            if ($section->getId() == $selectedTab ||
                !isset($selectedTab) && $section->getType() == CourseSection::TYPE_TOOL)
            {
                if ($section->getType() == CourseSection::TYPE_LINK)
                {
                    $content = $this->show_links($section);
                }
                else
                {

                    $content = $this->display_block_header($section, $sec_name);
                    $content .= $this->show_section_tools($section, $tools[$section->get_id()]);
                    $content .= $this->display_block_footer($section);
                    // if (($section->is_visible() && (count($tools[$section->get_id()]) > 0)) ||
                    // $this->is_course_admin)

                    if ($course_tool_layout >= 3)
                    {
                        // Temporary fix untill we move back to dynamic tabs
                        $content .= '<div class="removed-tools" style="display: none;"></div>';
                    }
                }
            }
        }

        $html[] = '</ul>';
        $html[] = '<div class="tool-list-content">';
        $html[] = $content;
        $html[] = '</div>';

        $html[] = '<script src="' . Path::getInstance()->getJavascriptPath('Chamilo\Application\Weblcms', true) .
            'CourseHome.js' . '"></script>';

        return implode(PHP_EOL, $html);
    }
}
