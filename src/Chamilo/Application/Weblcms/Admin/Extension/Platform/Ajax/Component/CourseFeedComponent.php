<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Feed to return course categories
 *
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class CourseFeedComponent extends AjaxManager
{
    const PARAM_OFFSET = 'offset';

    const PARAM_SEARCH_QUERY = 'query';

    const PROPERTY_ELEMENTS = 'elements';

    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    private $course_count = 0;

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
     * Returns the advanced element finder element for the given user
     *
     * @param User $user
     *
     * @return AdvancedElementFinderElement
     */
    protected function get_element_for_course($course)
    {
        $glyph = new FontAwesomeGlyph('chalkboard', array(), null, 'fas');

        return new AdvancedElementFinderElement(
            CourseEntity::ENTITY_TYPE . '_' . $course->get_id(), $glyph->getClassNamesString(), $course->get_title(),
            strip_tags($course->get_fully_qualified_name())
        );
    }

    /**
     * Returns all the elements for this feed
     *
     * @return AdvancedElementFinderElements
     */
    private function get_elements()
    {
        $elements = new AdvancedElementFinderElements();

        $glyph = new FontAwesomeGlyph('folder', array(), null, 'fas');

        // Add user category
        $course_category = new AdvancedElementFinderElement(
            'courses', $glyph->getClassNamesString(), Translation::get('Courses'), Translation::get('Courses')
        );
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
     * Returns the selected offset
     *
     * @return int
     */
    protected function get_offset()
    {
        $offset = Request::post(self::PARAM_OFFSET);
        if (!isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

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
                $search_query, array(Course::PROPERTY_TITLE, Course::PROPERTY_VISUAL_CODE)
            );
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

        $this->course_count = DataManager::count(
            Course::class_name(), new DataClassCountParameters($condition)
        );
        $parameters = new DataClassRetrievesParameters(
            $condition, 100, $this->get_offset(),
            array(new OrderBy(new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_TITLE)))
        );

        return DataManager::retrieves(Course::class_name(), $parameters);
    }

    public function set_course_count($course_count)
    {
        $this->course_count = $course_count;
    }
}
