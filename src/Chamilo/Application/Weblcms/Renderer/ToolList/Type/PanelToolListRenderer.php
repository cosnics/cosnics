<?php
namespace Chamilo\Application\Weblcms\Renderer\ToolList\Type;

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
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Application\Weblcms\Renderer\ToolList\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class PanelToolListRenderer extends ToolListRenderer
{

    /**
     *
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    public function getCourse()
    {
        return $this->get_parent()->get_course();
    }

    public function getCourseSectionByTool(CourseTool $tool)
    {
        $courseSettingsController = CourseSettingsController::getInstance();
        $sectionTypes = $this->getSectionTypes();

        $isToolVisible = $courseSettingsController->get_course_setting(
            $this->getCourse(), CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool->get_id()
        );

        if (!$isToolVisible && $tool->get_section_type() != CourseSection::TYPE_ADMIN)
        {
            return $sectionTypes[CourseSection::TYPE_DISABLED];
        }

        if ($tool->get_section_type() != CourseSection::TYPE_TOOL)
        {
            return $sectionTypes[$tool->get_section_type()];
        }

        $storedSectionIdentifier = $this->getSectionIdentifierByTool($tool);

        if (!is_null($storedSectionIdentifier))
        {
            return $storedSectionIdentifier;
        }

        return $sectionTypes[CourseSection::TYPE_TOOL];
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication[]
     */
    protected function getPublicationLinks()
    {
        if (!isset($this->publicationLinks))
        {
            $conditions = array();

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class_name(), ContentObjectPublication::PROPERTY_COURSE_ID
                ), new StaticConditionVariable($this->getCourse()->getId())
            );

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class_name(), ContentObjectPublication::PROPERTY_SHOW_ON_HOMEPAGE
                ), new StaticConditionVariable(1)
            );

            $condition = new AndCondition($conditions);

            $this->publicationLinks = DataManager::retrieves(
                ContentObjectPublication::class_name(), new DataClassRetrievesParameters($condition)
            )->as_array();
        }

        return $this->publicationLinks;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool $tool
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseToolRelCourseSection|NULL
     */
    public function getSectionIdentifierByTool(CourseTool $tool)
    {
        $courseSectionsForCourseTools = $this->getSectionIdentifiersForTools();

        if (array_key_exists($tool->getId(), $courseSectionsForCourseTools))
        {
            return $courseSectionsForCourseTools[$tool->getId()];
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @return integer[]
     */
    public function getSectionIdentifiersForTools()
    {
        if (!isset($this->sectionIdentifiersForTools))
        {
            $condition = new InCondition(
                new PropertyConditionVariable(
                    CourseToolRelCourseSection::class_name(), CourseToolRelCourseSection::PROPERTY_SECTION_ID
                ), $this->getSectionTypes()
            );

            $toolSectionRelations = DataManager::retrieves(
                CourseToolRelCourseSection::class_name(), new DataClassRetrievesParameters($condition)
            )->as_array();

            foreach ($toolSectionRelations as $toolSectionRelation)
            {
                $this->sectionIdentifiersForTools[$toolSectionRelation->get_tool_id()] =
                    $toolSectionRelation->get_section_id();
            }
        }

        return $this->sectionIdentifiersForTools;
    }

    /**
     *
     * @return integer[]
     */
    public function getSectionTypes()
    {
        if (!isset($this->sectionTypes))
        {
            $this->sectionTypes = array();

            $sections = $this->getSections();

            foreach ($sections as $section)
            {
                $this->sectionTypes[$section->get_type()] = $section->get_id();
            }
        }

        return $this->sectionTypes;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseSection[]
     */
    public function getSections()
    {
        if (!isset($this->sections))
        {
            $this->sections = array();

            $condition = new EqualityCondition(
                new PropertyConditionVariable(CourseSection::class_name(), CourseSection::PROPERTY_COURSE_ID),
                new StaticConditionVariable($this->getCourse()->get_id())
            );

            $orderProperty = array(
                new OrderBy(
                    new PropertyConditionVariable(CourseSection::class_name(), CourseSection::PROPERTY_DISPLAY_ORDER)
                )
            );

            $this->sections = DataManager::retrieves(
                CourseSection::class_name(), new DataClassRetrievesParameters($condition, null, null, $orderProperty)
            )->as_array();
        }

        return $this->sections;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseSection $section
     */
    public function getToolsBySection(CourseSection $section)
    {
        if (!isset($this->toolsBySection))
        {
            $visibleTools = $this->get_visible_tools();
            usort($visibleTools, array($this, 'sortToolsByName'));

            foreach ($visibleTools as $visibleTool)
            {
                if ($visibleTool->get_name() != 'home')
                {
                    $this->toolsBySection[$this->getCourseSectionByTool($visibleTool)][] = $visibleTool;
                }
            }
        }

        return $this->toolsBySection[$section->getId()];
    }

    /**
     *
     * @return boolean
     */
    protected function hasPublicationLinks()
    {
        $publicationLinks = $this->getPublicationLinks();

        return count($publicationLinks) > 0;
    }

    /**
     *
     * @return boolean
     */
    public function isCourseAdmin()
    {
        return $this->get_parent()->get_parent()->is_teacher();
    }

    /**
     * Show the links to publications in this course
     */
    private function renderLinks($section)
    {
        if (!$this->hasPublicationLinks())
        {
            return '';
        }

        $parent = $this->get_parent();
        $publications = $this->getPublicationLinks();

        $html = array();

        $html[] = '<h4>' . $section->getDisplayName() . '</h4>';

        $html[] = '<ul class="list-group list-group-course-tool">';

        foreach ($publications as $publication)
        {
            $html[] = '<li class="list-group-item">';

            if ($publication->is_hidden() == 0)
            {
                $link_class = '';
            }
            else
            {
                $link_class = ' class="invisible"';
            }

            $title = htmlspecialchars($publication->get_content_object()->get_title());

            if ($parent->is_allowed(WeblcmsRights::EDIT_RIGHT) || $publication->is_visible_for_target_users())
            {
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
                            'tool_action' => null, Manager::PARAM_COMPONENT_ACTION => null,
                            Manager::PARAM_TOOL => $publication->get_tool(),
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => $class::ACTION_VIEW,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication->get_id()
                        ), array(), true
                    );
                    $target = '';
                }

                $html[] = '<a href="' . $url . '"' . $target . $link_class . '>';
                $html[] = $title;
                $html[] = '</a>';
            }

            $html[] = '</li>';
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseSection $section
     *
     * @return string
     */
    private function renderToolsBySection($section)
    {
        if (!$this->sectionHasTools($section))
        {
            return '';
        }

        $tools = $this->getToolsBySection($section);

        $parent = $this->get_parent();

        $count = 0;

        $html = array();

        // $html[] = '<div class="panel panel-default panel-course-tool-list">';
        // $html[] = '<div class="panel-heading">';
        // $html[] = '<h3 class="panel-title">' . $section->getDisplayName() . '</h3>';
        // $html[] = '</div>';

        $html[] = '<h4>' . $section->getDisplayName() . '</h4>';

        $course_settings_controller = CourseSettingsController::getInstance();

        $html[] = '<ul class="list-group list-group-course-tool">';

        foreach ($tools as $tool)
        {

            $html[] = '<li class="list-group-item">';

            $toolNamespace = $tool->getContext();

            $toolIsVisible = $course_settings_controller->get_course_setting(
                $this->getCourse(), CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool->get_id()
            );

            if ($section->get_type() != CourseSection::TYPE_ADMIN && $toolIsVisible && $this->isCourseAdmin())
            {
                $visibilityUrl = $parent->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::ACTION_MAKE_TOOL_INVISIBLE,
                        \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager::PARAM_TOOL => $tool->get_name()
                    )
                );

                $html[] = '<div class="pull-right">';
                $html[] = '<a href="' . $visibilityUrl . '">';

                $glyph = new FontAwesomeGlyph('times', array(), null, 'fas');
                $html[] = $glyph->render();

                $html[] = '</a>';
                $html[] = '</div>';
            }

            $toolUrl = $parent->get_url(
                array(Manager::PARAM_TOOL => $tool->get_name()), array(
                    Manager::PARAM_COMPONENT_ACTION, \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE, Manager::PARAM_CATEGORY, true
                )
            );

            $html[] = '<a href="' . $toolUrl . '">';
            $html[] = Translation::get('TypeName', null, $toolNamespace);
            $html[] = '</a>';

            $html[] = '</li>';

            $count ++;
        }

        $html[] = '</ul>';

        // $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseSection $section
     *
     * @return boolean
     */
    public function sectionHasTools(CourseSection $section)
    {
        $tools = $this->getToolsBySection($section);

        return count($tools) > 0;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool $toolLeft
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool $toolRight
     *
     * @return integer
     */
    protected function sortToolsByName($toolLeft, $toolRight)
    {
        return strcmp(
            Translation::get('TypeName', null, $toolLeft->getContext()),
            Translation::get('TypeName', null, $toolRight->getContext())
        );
    }

    /**
     *
     * @see \Chamilo\Application\Weblcms\Renderer\ToolList\ToolListRenderer::toHtml()
     */
    public function toHtml()
    {
        $html = array();

        $html[] = '<div class="well">';

        foreach ($this->getSections() as $section)
        {
            $isAdminSectionType = ($section->get_type() == CourseSection::TYPE_DISABLED) ||
                ($section->get_type() == CourseSection::TYPE_ADMIN);

            if ((!$section->is_visible() || $isAdminSectionType) && !$this->isCourseAdmin())
            {
                continue;
            }

            switch ($section->get_type())
            {
                case CourseSection::TYPE_DISABLED :
                    break;
                case CourseSection::TYPE_LINK :
                    // $html[] = $this->renderLinks($section);
                    break;
                case CourseSection::TYPE_ADMIN :
                    break;
                default :
                    $html[] = $this->renderToolsBySection($section);
                    break;
            }
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
