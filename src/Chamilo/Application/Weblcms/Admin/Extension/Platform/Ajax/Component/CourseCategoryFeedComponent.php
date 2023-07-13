<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseCategoryEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;
use Chamilo\Libraries\Translation\Translation;

/**
 * Feed to return course categories
 *
 * @author  Sven Vanpoucke
 * @package application.weblcms
 */
class CourseCategoryFeedComponent extends Manager
{
    /**
     * The length for the filter prefix to remove
     */
    public const FILTER_PREFIX_LENGTH = 2;

    public const PARAM_COURSE = 'course';
    public const PARAM_COURSE_CATEGORY = 'course_category';
    public const PARAM_FILTER = 'filter';
    public const PARAM_OFFSET = 'offset';
    public const PARAM_SEARCH_QUERY = 'query';

    public const PROPERTY_ELEMENTS = 'elements';
    public const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    protected $course_count = 0;

    /**
     * Runs this ajax component
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $elements = $this->get_elements();
        $elements = $elements->as_array();

        $result->set_property(self::PROPERTY_ELEMENTS, $elements);

        if ($this->course_count > 0)
        {
            $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->course_count);
        }

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
     * Returns the element for a specific group
     *
     * @param $course_category
     *
     * @return AdvancedElementFinderElement
     */
    public function get_course_category_element($course_category)
    {
        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');

        return new AdvancedElementFinderElement(
            CourseCategoryEntity::ENTITY_TYPE . '_' . $course_category->get_id(), $glyph->getClassNamesString(),
            $course_category->get_name(), strip_tags($course_category->get_fully_qualified_name()),
            AdvancedElementFinderElement::TYPE_SELECTABLE_AND_FILTER
        );
    }

    /**
     * Returns the element for a specific user
     *
     * @param $course
     *
     * @return AdvancedElementFinderElement
     */
    public function get_course_element($course)
    {
        $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

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

        // Add course categories
        $course_categories = $this->retrieve_course_categories();
        if ($course_categories && $course_categories->count() > 0)
        {
            $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

            // Add course category category
            $course_category_category = new AdvancedElementFinderElement(
                'course_categories', $glyph->getClassNamesString(), Translation::get('CourseCategories'),
                Translation::get('CourseCategories')
            );
            $elements->add_element($course_category_category);

            foreach ($course_categories as $course_category)
            {
                $course_category_category->add_child($this->get_course_category_element($course_category));
            }
        }

        // Add courses
        $courses = $this->retrieve_courses();
        if ($courses && $courses->count() > 0)
        {
            $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');

            // Add user category
            $course_category = new AdvancedElementFinderElement(
                'courses', $glyph->getClassNamesString(), Translation::get('Courses'), Translation::get('Courses')
            );
            $elements->add_element($course_category);

            foreach ($courses as $course)
            {
                $course_category->add_child($this->get_course_element($course));
            }
        }

        return $elements;
    }

    /**
     * Returns the id of the selected filter
     */
    protected function get_filter()
    {
        $filter = $this->getRequest()->request->get(self::PARAM_FILTER);

        return substr($filter, static::FILTER_PREFIX_LENGTH);
    }

    protected function get_offset()
    {
        $offset = $this->getRequest()->request->get(self::PARAM_OFFSET);
        if (!isset($offset) || is_null($offset))
        {
            $offset = 0;
        }

        return $offset;
    }

    public function required_parameters()
    {
        return [];
    }

    /**
     * Returns all the groups for this feed
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory>
     */
    public function retrieve_course_categories()
    {
        // Set the conditions for the search query
        $search_query = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
        if ($search_query && $search_query != '')
        {
            $name_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_NAME), $search_query
            );

            $name_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_CODE), $search_query
            );

            $conditions[] = new OrCondition($name_conditions);
        }

        $filter_id = $this->get_filter();

        if ($filter_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_PARENT),
                new StaticConditionVariable($filter_id)
            );
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_PARENT),
                new StaticConditionVariable(0)
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

        return DataManager::retrieves(
            CourseCategory::class, new DataClassRetrievesParameters(
                $condition, null, null, new OrderBy([
                    new OrderProperty(
                        new PropertyConditionVariable(CourseCategory::class, CourseCategory::PROPERTY_NAME)
                    )
                ])
            )
        );
    }

    /**
     * Retrieves all the users for the selected group
     */
    private function retrieve_courses()
    {
        $conditions = [];

        $filter_id = $this->get_filter();

        if (!$filter_id)
        {
            return;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Course::class, Course::PROPERTY_CATEGORY_ID),
            new StaticConditionVariable($filter_id)
        );

        $search_query = $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);

        // Set the conditions for the search query
        if ($search_query && $search_query != '')
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $search_query, [
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE),
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_VISUAL_CODE)
                ]
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

        $this->course_count = \Chamilo\Application\Weblcms\Course\Storage\DataManager::count(
            Course::class, new DataClassCountParameters($condition)
        );

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(
            Course::class, new DataClassRetrievesParameters(
                $condition, 100, $this->get_offset(), new OrderBy(
                    [new OrderProperty(new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE))]
                )
            )
        );
    }
}
