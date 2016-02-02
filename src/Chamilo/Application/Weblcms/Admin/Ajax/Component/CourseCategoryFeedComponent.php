<?php
namespace Chamilo\Application\Weblcms\Admin\Ajax\Component;

use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Admin\Storage\DataManager;
use Chamilo\Application\Weblcms\Admin\Entity\CourseCategoryEntity;
use Chamilo\Application\Weblcms\Admin\Entity\CourseEntity;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Feed to return course categories
 *
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class CourseCategoryFeedComponent extends AjaxManager
{
    /**
     * The length for the filter prefix to remove
     */
    const FILTER_PREFIX_LENGTH = 2;
    const PARAM_COURSE_CATEGORY = 'course_category';
    const PARAM_COURSE = 'course';
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PARAM_FILTER = 'filter';
    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    protected $course_count = 0;

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $elements = $this->get_elements();
        $elements = $elements->as_array();

        $result->set_property(self :: PROPERTY_ELEMENTS, $elements);

        if ($this->course_count > 0)
        {
            $result->set_property(self :: PROPERTY_TOTAL_ELEMENTS, $this->course_count);
        }

        $result->display();
    }

    /**
     * Returns all the elements for this feed
     *
     * @return AdvancedElementFinderElements
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();

        // Add course categories
        $course_categories = $this->retrieve_course_categories();
        if ($course_categories && $course_categories->size() > 0)
        {
            // Add course category category
            $course_category_category = new AdvancedElementFinderElement(
                'course_categories',
                'category',
                Translation :: get('CourseCategories'),
                Translation :: get('CourseCategories'));
            $elements->add_element($course_category_category);

            while ($course_category = $course_categories->next_result())
            {
                $course_category_category->add_child($this->get_course_category_element($course_category));
            }
        }

        // Add courses
        $courses = $this->retrieve_courses();
        if ($courses && $courses->size() > 0)
        {
            // Add user category
            $course_category = new AdvancedElementFinderElement(
                'courses',
                'category',
                Translation :: get('Courses'),
                Translation :: get('Courses'));
            $elements->add_element($course_category);

            while ($course = $courses->next_result())
            {
                $course_category->add_child($this->get_course_element($course));
            }
        }

        return $elements;
    }

    /**
     * Retrieves all the users for the selected group
     */
    private function retrieve_courses()
    {
        $conditions = array();

        $filter_id = $this->get_filter();

        if (! $filter_id)
        {
            return;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_CATEGORY_ID),
            new StaticConditionVariable($filter_id));

        $search_query = Request :: post(self :: PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities :: query_to_condition(
                $search_query,
                array(Course :: PROPERTY_TITLE, Course :: PROPERTY_VISUAL_CODE));
        }

        // Combine the conditions
        $count = count($conditions);
        if ($count > 1)
        {
            $condition = new AndCondition($conditions);
        }

        if ($count == 1)
        {
            $condition = $conditions[0];
        }

        $this->course_count = \Chamilo\Application\Weblcms\Course\Storage\DataManager :: count(
            Course :: class_name(),
            $condition);

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager :: retrieves(
            Course :: class_name(),
            new DataClassRetrievesParameters(
                $condition,
                100,
                $this->get_offset(),
                array(new OrderBy(new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_TITLE)))));
    }

    protected function get_offset()
    {
        $offset = Request :: post(self :: PARAM_OFFSET);
        if (! isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    public function required_parameters()
    {
        return array();
    }

    /**
     * Returns all the groups for this feed
     *
     * @return ResultSet
     */
    public function retrieve_course_categories()
    {
        // Set the conditions for the search query
        $search_query = Request :: post(self :: PARAM_SEARCH_QUERY);
        if ($search_query && $search_query != '')
        {
            $q = '*' . $search_query . '*';
            $name_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_NAME),
                $q);
            $name_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_CODE),
                $q);
            $conditions[] = new OrCondition($name_conditions);
        }

        $filter_id = $this->get_filter();

        if ($filter_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_PARENT),
                new StaticConditionVariable($filter_id));
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_PARENT),
                new StaticConditionVariable(0));
        }

        // Combine the conditions
        $count = count($conditions);
        if ($count > 1)
        {
            $condition = new AndCondition($conditions);
        }

        if ($count == 1)
        {
            $condition = $conditions[0];
        }

        return DataManager :: retrieves(
            CourseCategory :: class_name(),
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                array(
                    new OrderBy(
                        new PropertyConditionVariable(CourseCategory :: class_name(), CourseCategory :: PROPERTY_NAME)))));
    }

    /**
     * Returns the element for a specific group
     *
     * @param \group\Group $group
     *
     * @return AdvancedElementFinderElement
     */
    public function get_course_category_element($course_category)
    {
        return new AdvancedElementFinderElement(
            CourseCategoryEntity :: ENTITY_TYPE . '_' . $course_category->get_id(),
            'type type_group',
            $course_category->get_name(),
            strip_tags($course_category->get_fully_qualified_name()),
            AdvancedElementFinderElement :: TYPE_SELECTABLE_AND_FILTER);
    }

    /**
     * Returns the element for a specific user
     *
     * @param \user\User $user
     *
     * @return AdvancedElementFinderElement
     */
    public function get_course_element($course)
    {
        return new AdvancedElementFinderElement(
            CourseEntity :: ENTITY_TYPE . '_' . $course->get_id(),
            'type type_user',
            $course->get_title(),
            strip_tags($course->get_fully_qualified_name()));
    }

    /**
     * Returns the id of the selected filter
     */
    protected function get_filter()
    {
        $filter = Request :: post(self :: PARAM_FILTER);
        return substr($filter, static :: FILTER_PREFIX_LENGTH);
    }
}
