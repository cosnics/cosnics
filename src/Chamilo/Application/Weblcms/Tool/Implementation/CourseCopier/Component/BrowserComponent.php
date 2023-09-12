<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Forms\CourseCopierForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Infrastructure\Repository\CourseCopierRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Infrastructure\Service\CourseCopier;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupService;
use Chamilo\Application\Weblcms\Tool\Service\PublicationSelectorDataMapper;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/*
 * This tool is for copying information from othe current course to another @author Mattias De Pauw - Hogeschool Gent
 */

class BrowserComponent extends Manager
{

    public $course_copier_form;

    private $category_parent_ids_mapping;

    /**
     * Check wether there can be anything coppied to a course and if there is even another course where the user can
     * copy to.
     * Build the form check validate and copy the selected information
     */
    public function run()
    {
        $course_id = $this->get_course_id();

        $courseGroupService = new CourseGroupService(new CourseGroupRepository());

        if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new NotAllowedException();
        }

        $contentObjectPublicationsCount = DataManager::count_course_content_object_publications(
            $course_id
        );

        $courseGroupsCount = $courseGroupService->countCourseGroupsInCourse($course_id);

        if ($contentObjectPublicationsCount == 0 && $courseGroupsCount <= 1)
        {
            throw new UserException(Translation::get('NoPublications'));
        }

        if (DataManager::count_courses_from_user_where_user_is_teacher(
                $this->get_user()
            ) <= 1)
        {
            throw new UserException(Translation::get('NoCoursesToCopy'));
        }

        $publicationSelectorDataMapper = new PublicationSelectorDataMapper();

        $publications = $publicationSelectorDataMapper->getContentObjectPublicationsForPublicationSelector($course_id);
        $categories = $publicationSelectorDataMapper->getContentObjectPublicationCategoriesForPublicationSelector(
            $course_id
        );

        $courses = DataManager::retrieve_courses_from_user_where_user_is_teacher(
            $this->get_parent()->get_user()
        );

        $this->course_copier_form = new CourseCopierForm($this, $publications, $categories, $courses);
        $this->course_copier_form->buildForm();

        if ($this->course_copier_form->validate())
        {
            $values = $this->course_copier_form->exportValues();

            $course_ids = $values['course'];
            foreach ($course_ids as $course_id)
            {
                $course = DataManager::retrieve_by_id(
                    Course::class, $course_id
                );
                if (!$course->is_course_admin($this->get_user()))
                {
                    throw new NotAllowedException();
                }
            }

            if (isset($values['publications']) || isset($values['course_sections']) ||
                $values['content_object_categories'] == 0 || $values['course_groups'] == 1)
            {
                $publications_ids = array_keys($values['publications']);
                $ignore_categories = $values['content_object_categories'];
                $categories_ids = array_keys($values['categories']);
                $copyCourseGroups = boolval($values['course_groups']);

                $courseCopier = new CourseCopier(new CourseCopierRepository());

                $courseCopier->copyCourse(
                    $this->getUser(), $this->get_course(), $course_ids, $publications_ids, $categories_ids,
                    $ignore_categories, $copyCourseGroups
                );

                $this->redirectWithMessage(
                    Translation::get('CopySucceeded'), false, [
                        \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_WEBLCMS_HOME
                    ]
                );
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = Display::error_message(Translation::get('SelectAItem'));
                $html[] = $this->course_copier_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();

            $html[] = '<div class="alert alert-warning">' .
                Translation::getInstance()->getTranslation('CopyNotification', [], Manager::CONTEXT) . '</div>';

            $html[] = $this->course_copier_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
    }
}
