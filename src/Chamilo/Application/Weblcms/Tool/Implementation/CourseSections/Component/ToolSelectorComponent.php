<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\CourseSectionToolSelectorForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: course_sections_tool_selector.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_sections.component
 */
class ToolSelectorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }
        $id = Request :: get(self :: PARAM_COURSE_SECTION_ID);
        if ($id)
        {
            $course_section = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                CourseSection :: class_name(),
                (int) $id);

            $form = new CourseSectionToolSelectorForm(
                $course_section,
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_SELECT_TOOLS_COURSE_SECTION,
                        self :: PARAM_COURSE_SECTION_ID => $id)));

            if ($form->validate())
            {
                $success = $form->update_course_modules();
                $this->redirect(
                    Translation :: get($success ? 'CourseSectionUpdated' : 'CourseSectionNotUpdated'),
                    ($success ? false : true),
                    array(self :: PARAM_ACTION => self :: ACTION_VIEW_COURSE_SECTIONS));
            }
            else
            {
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_VIEW_COURSE_SECTIONS)),
                        $course_section->get_name()));
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SELECT_TOOLS_COURSE_SECTION,
                                self :: PARAM_COURSE_SECTION_ID => $id)),
                        Translation :: get('SelectTools')));

                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation :: get('NoCourseSectionSelected')));
        }
    }
}
