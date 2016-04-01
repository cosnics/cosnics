<?php
namespace Chamilo\Application\Weblcms\Renderer\CourseList\Type;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategoryRelCourse;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Storage\ResultSet\RecordResultSet;

/**
 * Course list renderer to render the course list filtered by a given course type and user course category
 *
 * @author Sven Vanpoucke
 */
class FilteredCourseListRenderer extends CourseListRenderer
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The filtered course type id
     *
     * @var int
     */
    private $course_type_id;

    /**
     * The filtered user course category id
     *
     * @var int
     */
    private $user_course_category_id;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Shows a course list filtered on a given course type and user course category id
     *
     * @param mixed $parent
     * @param string $target
     * @param int $course_type_id - [OPTIONAL] default 0
     * @param int $user_course_category_id - [OPTIONAL] default null
     */
    public function __construct($parent, $target = '', $course_type_id = 0, $user_course_category_id = null)
    {
        parent :: __construct($parent, $target);

        $this->set_course_type_id($course_type_id);
        $this->set_user_course_category_id($user_course_category_id);
    }

    /**
     * Returns the conditions needed to retrieve the courses
     *
     * @return Condition
     */
    protected function get_retrieve_courses_condition()
    {
        $course_type_id = $this->get_course_type_id();

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_COURSE_TYPE_ID),
            new StaticConditionVariable($course_type_id));

        $user_course_category_id = $this->get_user_course_category_id();
        if (! is_null($user_course_category_id))
        {
            $course_user_category_conditions = array();

            $course_user_category_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory :: class_name(),
                    CourseTypeUserCategory :: PROPERTY_COURSE_USER_CATEGORY_ID),
                new StaticConditionVariable($user_course_category_id));

            $course_user_category_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategory :: class_name(),
                    CourseTypeUserCategory :: PROPERTY_COURSE_TYPE_ID),
                new StaticConditionVariable($course_type_id));

            $course_user_category_condition = new AndCondition($course_user_category_conditions);

            // retrieve course user categories

            $course_type_user_categories = DataManager :: retrieves(
                CourseTypeUserCategory :: class_name(),
                new DataClassRetrievesParameters($course_user_category_condition));

            $course_type_user_category_ids = array();
            while ($course_type_user_category = $course_type_user_categories->next_result())
            {
                $course_type_user_category_ids[] = $course_type_user_category->get_id();
            }

            $course_type_user_category_condition = new InCondition(
                new PropertyConditionVariable(
                    CourseTypeUserCategoryRelCourse :: class_name(),
                    CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID),
                $course_type_user_category_ids);

            $course_type_user_category_rel_courses = DataManager :: retrieves(
                CourseTypeUserCategoryRelCourse :: class_name(),
                new DataClassRetrievesParameters($course_type_user_category_condition));

            $course_type_user_category_rel_course_ids = array();
            while ($course_type_user_category_rel_course = $course_type_user_category_rel_courses->next_result())
            {
                $course_type_user_category_rel_course_ids[] = $course_type_user_category_rel_course->get_course_id();
            }

            $conditions[] = new Incondition(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_ID),
                $course_type_user_category_rel_course_ids);
        }

        return new AndCondition($conditions);
    }

    /**
     * Retrieves the courses for the user
     */
    protected function retrieve_courses()
    {
        $courseObjects = array();

        $courses = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_courses_with_user_course_categories(
            $this->get_parent()->get_user(), $this->get_retrieve_courses_condition()
        );

        if(!$this->get_user_course_category_id())
        {
            $courses = $this->order_courses_by_user_course_categories($courses);
        }

        while($course = $courses->next_result())
        {
            $courseObjects[] = DataClass :: factory(Course :: class_name(), $course);
        }

        return new ArrayResultSet($courseObjects);
    }

    /**
     * Order the courses by the user course categories
     *
     * @param RecordResultSet $courses
     *
     * @return ArrayResultSet
     */
    protected function order_courses_by_user_course_categories($courses)
    {
        $coursesByCourseCategories = array();

        while($course = $courses->next_result())
        {
            $category_id = $course[CourseTypeUserCategoryRelCourse :: PROPERTY_COURSE_TYPE_USER_CATEGORY_ID];
            $category = !is_null($category_id) ? $category_id : 0;
            $coursesByCourseCategories[$category][] = $course;
        }

        $userCategories = $this->retrieve_course_user_categories_for_course_type($this->get_course_type_id());

        $orderedCourses = array();

        if(is_array($coursesByCourseCategories[0]))
        {
            $orderedCourses = array_merge($orderedCourses, $coursesByCourseCategories[0]);
        }

        while($userCategory = $userCategories->next_result())
        {
            $coursesByCourseCategory = $coursesByCourseCategories[$userCategory[CourseTypeUserCategory::PROPERTY_ID]];

            if(is_array($coursesByCourseCategory))
            {
                $orderedCourses = array_merge($orderedCourses, $coursesByCourseCategory);
            }
        }

        return new ArrayResultSet($orderedCourses);
    }

    /**
     * Retrieves the course user categories for a course type
     *
     * @param int $course_type_id
     *
     * @return RecordResultSet
     */
    protected function retrieve_course_user_categories_for_course_type($course_type_id)
    {
        return DataManager ::retrieve_course_user_categories_from_course_type(
            $course_type_id,
            $this->get_parent()->get_user_id()
        );
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the course type id
     *
     * @return int
     */
    public function get_course_type_id()
    {
        return $this->course_type_id;
    }

    /**
     * Sets the course type id
     *
     * @param int $course_type_id
     */
    public function set_course_type_id($course_type_id)
    {
        $this->course_type_id = $course_type_id;
    }

    /**
     * Returns the user course category id
     *
     * @return int
     */
    public function get_user_course_category_id()
    {
        return $this->user_course_category_id;
    }

    /**
     * Sets the user course category id
     *
     * @param int $user_course_category_id
     */
    public function set_user_course_category_id($user_course_category_id)
    {
        $this->user_course_category_id = $user_course_category_id;
    }

    /**
     * Defines the display of the message when there are no courses to display.
     */
    protected function get_no_courses_message_as_html()
    {
        return '<div class="panel-body">' . Translation :: get('NoCoursesMatchSearchCriteria') . '</div>';
    }
}
