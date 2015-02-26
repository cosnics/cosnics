<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Forms\CourseTruncaterForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseTruncater\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/*
 * Component for emptying the course publication,publication categories and sections @author Maarten Volckaert -
 * Hogeschool Gent @author Mattias De Pauw - Hogeschool Gent
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    public $course_emptier_form;

    /**
     * checks whether the user has the rights if so create empty form if validate delete selected publications section.
     */
    public function run()
    {
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

        $this->course_emptier_form = new CourseTruncaterForm($this);
        $this->course_emptier_form->buildForm();

        if ($this->course_emptier_form->validate())
        {
            $values = $this->course_emptier_form->exportValues();

            if (isset($values['publications']) || isset($values["course_sections"]) ||
                 $values['content_object_categories'] == 1)
            {
                // always first delete the publications before deleting the categories

                $succes_publications = $this->empty_publications($values);
                $succes_sections = $this->empy_sections($values);
                $succes_categories = $this->empty_categories($values);

                if ($succes_publications && $succes_sections && $succes_categories)
                {
                    $this->redirect(
                        Translation :: get('AllSelectedObjectsRemoved'),
                        false,
                        array(
                            \Chamilo\Application\Weblcms\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_WEBLCMS_HOME));
                }
                else
                {
                    throw new \Exception(Translation :: get('NotAllSelectedObjectsRemoved'));
                }
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = Display :: error_message(Translation :: get('SelectAItem'));
                $html[] = $this->course_emptier_form->toHtml();
                $html[] = $this->render_footer();

                return implode("\n", $html);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->course_emptier_form->toHtml();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
    }

    /**
     * delete the selected publications
     *
     * @param type $values
     * @return succes
     */
    public function empty_publications($values)
    {
        $publication_ids = array_keys($values['publications']);

        $succes = true;

        foreach ($publication_ids as $id)
        {
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
                ContentObjectPublication :: class_name(),
                $id);

            if (! $publication->delete())
            {
                $succes = false;
            }
        }
        return $succes;
    }

    /**
     * delete the selected sections
     *
     * @param type $values
     * @return succes
     */
    public function empy_sections($values)
    {
        $succes = true;

        $course_section_ids = array_keys($values['course_sections']);
        $condition = new InCondition(
            new PropertyConditionVariable(CourseSection :: class_name(), CourseSection :: PROPERTY_ID),
            $course_section_ids);

        $course_sections = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            CourseSection :: class_name(),
            $condition);

        while ($course_section = $course_sections->next_result())
        {
            if (! $course_section->delete())
            {
                $succes = false;
            }
        }
        return $succes;
    }

    /**
     * delete the selected categories
     *
     * @param type $values
     * @return succes
     */
    public function empty_categories($values)
    {
        $succes = true;

        if ($values['content_object_categories'] == 1)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublicationCategory :: PROPERTY_COURSE),
                new StaticConditionVariable($this->get_course_id()));

            $categories = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
                ContentObjectPublicationCategory :: class_name(),
                $condition);

            while ($category = $categories->next_result())
            {
                if (! $category->get_allow_change() || ! $this->allowed_to_delete_category($category->get_id()))
                {
                    continue;
                }

                if (! $category->delete())
                {
                    $succes = false;
                }
            }
        }

        return $succes;
    }

    /**
     * checks whether a category can be deleted
     *
     * @param int $category_id
     * @return boolean
     */
    private function allowed_to_delete_category($category_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CATEGORY_ID),
            new StaticConditionVariable($category_id));
        $condition = new AndCondition($conditions);
        $count = \Chamilo\Application\Weblcms\Storage\DataManager :: count_content_object_publications($condition);

        if ($count > 0)
        {
            return false;
        }

        return ! $this->have_subcategories_publications($category_id);
    }

    /**
     * checks if a category has subcategory's
     *
     * @param int $category_id
     *
     * @return boolean
     */
    private function have_subcategories_publications($category_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_PARENT),
            new StaticConditionVariable($category_id));

        $subcategries = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublicationCategory :: class_name(),
            $condition);

        while ($cat = $subcategries->next_result())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_COURSE_ID),
                new StaticConditionVariable($this->get_course_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_CATEGORY_ID),
                new StaticConditionVariable($cat->get_id()));
            $condition = new AndCondition($conditions);
            $count = \Chamilo\Application\Weblcms\Storage\DataManager :: count_content_object_publications($condition);

            if ($count > 0 || $this->have_subcategories_publications($cat->get_id()))
            {
                return true;
            }
        }
        return false;
    }
}
