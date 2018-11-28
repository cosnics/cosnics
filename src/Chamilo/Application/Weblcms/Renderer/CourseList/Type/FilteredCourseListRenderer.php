<?php

namespace Chamilo\Application\Weblcms\Renderer\CourseList\Type;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Renderer\CourseList\CourseListRenderer;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\CourseUserCategoryService;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTypeUserCategoryRelCourse;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
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
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    protected $courseService;

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseUserCategoryService
     */
    protected $courseUserCategoryService;

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
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Chamilo\Application\Weblcms\Service\CourseUserCategoryService $courseUserCategoryService
     */
    public function __construct(
        $parent, $target = '', $course_type_id = 0, $user_course_category_id = null, CourseService $courseService,
        CourseUserCategoryService $courseUserCategoryService
    )
    {
        parent::__construct($parent, $target);

        $this->set_course_type_id($course_type_id);
        $this->set_user_course_category_id($user_course_category_id);
        $this->courseService = $courseService;
        $this->courseUserCategoryService = $courseUserCategoryService;
    }

    /**
     * Retrieves the courses for the user
     */
    protected function retrieve_courses()
    {
        if($this->get_course_type_id() >= 0)
        {
            $courseType = new CourseType();
            $courseType->setId($this->get_course_type_id());

            if ($this->get_user_course_category_id() > 0)
            {
                $courseUserCategory = new CourseUserCategory();
                $courseUserCategory->setId($this->get_user_course_category_id());

                return $this->courseUserCategoryService->getCoursesForUserByCourseUserCategoryAndCourseType(
                    $this->get_parent()->getUser(), $courseUserCategory, $courseType
                );
            }

            return new ArrayResultSet($this->courseService->getCoursesInCourseTypeForUser($this->get_parent()->getUser(), $courseType));
        }

        return new ArrayResultSet($this->courseService->getAllCoursesForUser($this->get_parent()->getUser()));
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
        return '<div class="panel-body">' . Translation::get('NoCoursesMatchSearchCriteria') . '</div>';
    }
}
