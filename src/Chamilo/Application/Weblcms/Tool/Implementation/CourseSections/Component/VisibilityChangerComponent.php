<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component
 */
class VisibilityChangerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $ids = Request::get(self::PARAM_COURSE_SECTION_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $course_section = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                    CourseSection::class_name(),
                    (int) $id);

                $course_section->set_visible(! $course_section->is_visible());

                if (! $course_section->update())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCourseSectionVisibilityChanged';
                }
                else
                {
                    $message = 'SelectedCourseSectionVisibilityChanged';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCourseSectionsVisibilityChanged';
                }
                else
                {
                    $message = 'SelectedCourseSectionsVisibilityChanged';
                }
            }

            $this->redirect(
                Translation::get($message),
                ($failures != 0 ? true : false),
                array(self::PARAM_ACTION => self::ACTION_VIEW_COURSE_SECTIONS));
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation::get('NoCourseSectionsSelected')));
        }
    }
}
