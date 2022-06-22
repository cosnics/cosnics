<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component
 */
class MoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new NotAllowedException();
        }

        $id = Request::get(self::PARAM_COURSE_SECTION_ID);
        $direction = Request::get(self::PARAM_DIRECTION);

        if (! empty($id))
        {
            $course_section = DataManager::retrieve_by_id(
                CourseSection::class,
                $id);
            $course_section->set_display_order($course_section->get_display_order() + $direction);
            $success = $course_section->update();

            $message = $success ? 'CourseSectionMoved' : 'CourseSectionNotMoved';

            $this->redirectWithMessage(
                Translation::get($message),
                (! $success),
                array(self::PARAM_ACTION => self::ACTION_VIEW_COURSE_SECTIONS));
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation::get('NoCourseSectionsSelected')));
        }
    }
}
