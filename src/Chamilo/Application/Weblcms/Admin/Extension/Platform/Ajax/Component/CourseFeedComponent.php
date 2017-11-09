<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * Feed to return course categories
 *
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class CourseFeedComponent extends AjaxManager
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';
    const PROPERTY_ELEMENTS = 'elements';

    private $course_count = 0;

    /**
     * Returns the required parameters
     *
     * @return string[]
     */
    public function required_parameters()
    {
        return array();
    }

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $search_query = Request::post(self::PARAM_SEARCH_QUERY);

        $elements = $this->get_elements();

        $elements = $elements->as_array();

        $result->set_property(self::PROPERTY_ELEMENTS, $elements);
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->user_count);

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

        // Add user category
        $course_category = new AdvancedElementFinderElement(
            'courses',
            'category',
            Translation::get('Courses'),
            Translation::get('Courses'));
        $elements->add_element($course_category);

        $courses = $this->retrieve_courses();
        if ($courses)
        {
            while ($course = $courses->next_result())
            {
                $course_category->add_child($this->get_element_for_course($course));
            }
        }

        return $elements;
    }

    /**
     * Retrieves the users from the course (direct subscribed and group subscribed)
     *
     * @return ResultSet
     */
    public function retrieve_courses()
    {
        $search_query = Request::post(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = Utilities::query_to_condition(
                $search_query,
                array(Course::PROPERTY_TITLE, Course::PROPERTY_VISUAL_CODE));
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

        $this->course_count = \Chamilo\Application\Weblcms\Course\Storage\DataManager::count(
            Course::class_name(),
            new DataClassCountParameters($condition));
        $parameters = new DataClassRetrievesParameters(
            $condition,
            100,
            $this->get_offset(),
            array(new OrderBy(new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_TITLE))));

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(Course::class_name(), $parameters);
    }

    /**
     * Returns the selected offset
     *
     * @return int
     */
    protected function get_offset()
    {
        $offset = Request::post(self::PARAM_OFFSET);
        if (! isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    /**
     * Returns the advanced element finder element for the given user
     *
     * @param User $user
     *
     * @return AdvancedElementFinderElement
     */
    protected function get_element_for_course($course)
    {
        return new AdvancedElementFinderElement(
            CourseEntity::ENTITY_TYPE . '_' . $course->get_id(),
            'type type_course',
            $course->get_title(),
            strip_tags($course->get_fully_qualified_name()));
    }

    public function set_course_count($course_count)
    {
        $this->course_count = $course_count;
    }
}
