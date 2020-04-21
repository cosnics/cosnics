<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user = $this->get_user();

        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new NotAllowedException();
        }

        $ids = $this->getRequest()->get(self::PARAM_COURSE_SECTION_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                /** @var CourseSection $course_section */
                $course_section = DataManager::retrieve_by_id(
                    CourseSection::class_name(),
                    $id);

                if ($course_section->get_type() != CourseSection::TYPE_CUSTOM || ! $course_section->delete())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCourseSectionNotDeleted';
                }
                else
                {
                    $message = 'SelectedCourseSectionsNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedCourseSectionDeleted';
                }
                else
                {
                    $message = 'SelectedCourseSectionsDeleted';
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
