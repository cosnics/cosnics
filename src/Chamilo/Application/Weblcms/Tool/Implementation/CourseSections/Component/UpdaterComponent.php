<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\CourseSectionForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();

        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new NotAllowedException();
        }

        $id = Request::get(self::PARAM_COURSE_SECTION_ID);
        if (! empty($id))
        {
            $course_section = DataManager::retrieve_by_id(
                CourseSection::class,
                (int) $id);

            $form = new CourseSectionForm(
                CourseSectionForm::TYPE_EDIT,
                $course_section,
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_UPDATE_COURSE_SECTION,
                        self::PARAM_COURSE_SECTION_ID => $id)));

            if ($form->validate())
            {
                $success = $form->update_course_section();
                $this->redirect(
                    Translation::get($success ? 'CourseSectionUpdated' : 'CourseSectionNotUpdated'),
                    ($success ? false : true),
                    array(self::PARAM_ACTION => self::ACTION_VIEW_COURSE_SECTIONS));
            }
            else
            {
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_COURSE_SECTIONS)),
                        $course_section->get_name()));
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_UPDATE_COURSE_SECTION,
                                self::PARAM_COURSE_SECTION_ID => $id)),
                        Translation::get('Update', null, Utilities::COMMON_LIBRARIES)));

                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(Translation::get('NoCourseSectionSelected'));
        }
    }
}
