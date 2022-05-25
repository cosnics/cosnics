<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
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
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\Translation\Translation;

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
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
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
        $glyph = new FontAwesomeGlyph('chalkboard', [], null, 'fas');

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

        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

        // Add user category
        $course_category = new AdvancedElementFinderElement(
            'courses', $glyph->getClassNamesString(), Translation::get('Courses'), Translation::get('Courses')
        );
        $elements->add_element($course_category);

        $courses = $this->retrieve_courses();
        if ($courses)
        {
            foreach ($courses as $course)
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
        return [];
    }

    /**
     * Retrieves the users from the course (direct subscribed and group subscribed)
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieve_courses()
    {
        $search_query = Request::post(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $search_query, array(
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE),
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_VISUAL_CODE)
                )
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
            Course::class, new DataClassCountParameters($condition)
        );
        $parameters = new DataClassRetrievesParameters(
            $condition, 100, $this->get_offset(),
            new OrderBy(array(new OrderProperty(new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE))))
        );

        return DataManager::retrieves(Course::class, $parameters);
    }

    public function set_course_count($course_count)
    {
        $this->course_count = $course_count;
    }
}
