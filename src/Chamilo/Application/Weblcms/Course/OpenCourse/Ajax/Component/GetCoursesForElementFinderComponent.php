<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Ajax\Component;

use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Returns the courses formatted for the element finder
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetCoursesForElementFinderComponent
    extends \Chamilo\Application\Weblcms\Course\Ajax\Component\GetCoursesForElementFinderComponent
{

    /**
     * Retrieves the courses for the current request
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function getCourses()
    {
        return $this->getOpenCourseService()->getClosedCourses(
            $this->getCondition(), $this->ajaxResultGenerator->getOffset(), 100,
            new OrderBy(array(new OrderProperty(new PropertyConditionVariable(Course::class, Course::PROPERTY_TITLE))))
        );
    }

    /**
     *
     * @return OpenCourseService
     */
    public function getOpenCourseService()
    {
        return $this->getService(OpenCourseService::class);
    }

    /**
     * Returns the number of total elements (without the offset)
     *
     * @return int
     */
    public function getTotalNumberOfElements()
    {
        return $this->getOpenCourseService()->countClosedCourses($this->getCondition());
    }
}