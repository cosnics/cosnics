<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Forms\CourseCopierForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Manager;
use Chamilo\Application\Weblcms\Tool\Service\PublicationSelectorDataMapper;
use Chamilo\Core\Rights\RightsLocation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/*
 * This tool is for copying information from othe current course to another @author Mattias De Pauw - Hogeschool Gent
 */

class BrowserComponent extends Manager
{

    public $course_copier_form;

    private $category_parent_ids_mapping;

    /**
     * Check wether there can be anything coppied to a course and if there is even another course where the user can
     * copy to. Build the form check validate and copy the selected information
     */
    public function run()
    {
        $course_id = $this->get_course_id();

        // $trail = BreadcrumbTrail :: get_instance();
        if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        if (\Chamilo\Application\Weblcms\Course\Storage\DataManager::
            count_course_content_object_publications($course_id) == 0
        )
        {
            throw new \Exception(Translation:: get('NoPublications'));
        }

        if (\Chamilo\Application\Weblcms\Course\Storage\DataManager::
            count_courses_from_user_where_user_is_teacher($this->get_user()) <= 1
        )
        {
            throw new \Exception(Translation:: get('NoCoursesToCopy'));
        }

        $publicationSelectorDataMapper = new PublicationSelectorDataMapper();

        $publications = $publicationSelectorDataMapper->getContentObjectPublicationsForPublicationSelector($course_id);
        $categories = $publicationSelectorDataMapper->getContentObjectPublicationCategoriesForPublicationSelector(
            $course_id
        );

        $courses =
            \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_courses_from_user_where_user_is_teacher(
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
                $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager:: retrieve_by_id(
                    Course:: class_name(),
                    $course_id
                );
                if (!$course->is_course_admin($this->get_user()))
                {
                    throw new NotAllowedException();
                }
            }

            if (isset($values['publications']) || isset($values["course_sections"]) ||
                $values['content_object_categories'] == 0
            )
            {
                $success = true;
                $publications_ids = array_keys($values['publications']);
                $ignore_categories = $values['content_object_categories'];
                $categories_ids = array_keys($values['categories']);

                if ($ignore_categories == 0 && count($categories_ids) > 0)
                {
                    $success =
                        \Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Storage\DataManager::copy_publications_and_categories(
                            $course_ids, $publications_ids, $categories_ids
                        );
                }
                else
                {
                    $success =
                        \Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Storage\DataManager::copy_publications_to_root(
                            $course_ids, $publications_ids
                        );
                }

                if ($success)
                {
                    $this->redirect(
                        Translation:: get('CopySucceeded'),
                        false,
                        array(
                            \Chamilo\Application\Weblcms\Manager :: PARAM_ACTION =>
                                \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_WEBLCMS_HOME
                        )
                    );
                }
                else
                {
                    throw new \Exception(Translation:: get('CopyNotSucceeded'));
                }
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = Display:: error_message(Translation:: get('SelectAItem'));
                $html[] = $this->course_copier_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();

            $html[] = Display::normal_message(
                Translation::getInstance()->getTranslation('CopyNotification', array(), Manager::context())
            );

            $html[] = $this->course_copier_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
