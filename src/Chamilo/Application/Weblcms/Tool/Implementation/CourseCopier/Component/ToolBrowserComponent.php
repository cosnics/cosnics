<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\CourseUserRelation;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Forms\CourseCopierForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseCopier\Manager;
use Chamilo\Core\Rights\RightsLocation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Display;
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
class ToolBrowserComponent extends Manager implements DelegateComponent
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
        // $trail = BreadcrumbTrail :: get_instance();
        if (! $this->get_course()->is_course_admin($this->get_parent()->get_user()))
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $sections_founded = false;
        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseSection :: class_name(), CourseSection :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id()));
        $course_sections = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            CourseSection :: class_name(),
            $condition);

        $common_sections = array(
            CourseSection :: TYPE_TOOL,
            CourseSection :: TYPE_DISABLED,
            CourseSection :: TYPE_LINK,
            CourseSection :: TYPE_ADMIN);

        while ($course_section = $course_sections->next_result())
        {
            if (! in_array($course_section->get_type(), $common_sections))
            {
                $sections_founded = true;
            }
        }
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id()));
        if (\Chamilo\Application\Weblcms\Storage\DataManager :: count_content_object_publications($condition) == 0 &&
             ! $sections_founded)
        {
            throw new \Exception(Translation :: get('NoPublications'));
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(CourseUserRelation :: class_name(), CourseUserRelation :: PROPERTY_STATUS),
            new StaticConditionVariable(1));
        if (\Chamilo\Application\Weblcms\Storage\DataManager :: count(CourseUserRelation :: class_name(), $condition) <=
             1)
        {
            throw new \Exception(Translation :: get('NoCoursesToCopy'));
        }

        $this->course_copier_form = new CourseCopierForm($this);
        $this->course_copier_form->buildForm();

        if ($this->course_copier_form->validate())
        {
            $values = $this->course_copier_form->exportValues();

            $course_ids = $values['course'];
            foreach ($course_ids as $course_id)
            {
                $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieve_by_id(
                    Course :: class_name(),
                    $course_id);
                if (! $course->is_course_admin($this->get_user()))
                {
                    throw new NotAllowedException();
                }
            }

            if (isset($values['publications']) || isset($values["course_sections"]) ||
                 $values['content_object_categories'] == 1)
            {
                // Before copying always first copy the categories !!!!!!

                $succes_categories = true;
                if ($values['content_object_categories'] == 1)
                {
                    $succes_categories = $this->copy_categories(0, $course_ids);
                }

                $succes_publications = $this->copy_publications($values);
                // $succes_sections = $this->copy_sections($values);

                if ($succes_publications && $succes_categories)
                {
                    $this->redirect(
                        Translation :: get('CopySucceeded'),
                        false,
                        array(
                            \Chamilo\Application\Weblcms\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_WEBLCMS_HOME));
                }
                else
                {
                    throw new \Exception(Translation :: get('CopyNotSucceeded'));
                }
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = Display :: error_message(Translation :: get('SelectAItem'));
                $html[] = $this->course_copier_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->course_copier_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Copy the selected sections
     */
    public function copy_sections($values)
    {
        $success = true;
        $this->parent_ids_mapping = array();

        $courses = $values['course'];

        $course_section_ids = array_keys($values['course_sections']);
        $condition = new InCondition(
            new PropertyConditionVariable(CourseSection :: class_name(), CourseSection :: PROPERTY_ID),
            $course_section_ids);

        $course_sections = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            CourseSection :: class_name(),
            $condition);

        while ($course_section = $course_sections->next_result())
        {
            foreach ($courses as $course_code)
            {
                $course_section->set_id(null);
                $course_section->set_course_id($course_code);

                if (! $course_section->create())
                    $success = false;
            }
        }
        return $success;
    }

    /**
     * Copy the selected publications in the right category
     */
    public function copy_publications($values)
    {
        $success = true;

        $publication_ids = array_keys($values['publications']);
        foreach ($publication_ids as $id)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                ContentObjectPublication :: class_name(),
                $id);

            $possible_publication_class = 'application\weblcms\tool\\' . $publication->get_tool() . '\\Publication';

            if (class_exists($possible_publication_class))
            {
                $datamanager_class = 'application\weblcms\tool\\' . $publication->get_tool() . '\\DataManager';

                $publication_extension = $datamanager_class :: retrieve(
                    $possible_publication_class,
                    new DataClassRetrieveParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                $possible_publication_class :: class_name(),
                                $possible_publication_class :: PROPERTY_PUBLICATION_ID),
                            new StaticConditionVariable($publication->get_id()))));
            }

            $courses = $values['course'];
            $parent = $publication->get_category_id();

            foreach ($courses as $course_code)
            {
                $publication->set_id(null);
                $publication->set_course_id($course_code);

                if ($parent != 0)
                {
                    $publication->set_category_id($this->category_parent_ids_mapping[$course_code][$parent]);
                }

                $result = $publication->create();
                if (! ($result instanceof RightsLocation))
                {
                    $success = false;
                }
                else
                {
                    if ($publication_extension instanceof DataClass)
                    {
                        $publication_extension->set_publication_id($publication->get_id());
                        $success = $publication_extension->create();
                    }
                    else
                    {
                        $success = false;
                    }
                }
            }
        }
        return $success;
    }

    /**
     * copy the categories to the other course
     *
     * @param int $parent_id
     * @param type $courses
     */
    public function copy_categories($parent_id, $courses)
    {
        $success = true;

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_COURSE),
            new StaticConditionVariable($this->get_course_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_PARENT),
            new StaticConditionVariable($parent_id));
        $condition = new AndCondition($conditions);

        $order_by = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER));

        $categories = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublicationCategory :: class_name(),
            new DataClassRetrievesParameters($condition, null, null, $order_by));

        while ($category = $categories->next_result())
        {
            if (! $category->get_allow_change())
            {
                continue; // Because the course groups are not copied this won't copy their corresponding folders
            }

            $old_id = $category->get_id();

            foreach ($courses as $course_code)
            {
                $category->set_id(null);
                $category->set_course($course_code);

                if ($parent_id != 0)
                {
                    $category->set_parent($this->category_parent_ids_mapping[$course_code][$parent_id]);
                }

                if (! $category->create())
                    $success = false;

                $this->category_parent_ids_mapping[$course_code][$old_id] = $category->get_id();
            }

            // copy the children
            if (! $this->copy_categories($old_id, $courses))
                $success = false;
        }

        return $success;
    }
}
