<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Table;

use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTableDataProvider;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * DataProvider for open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseTableDataProvider extends CourseTableDataProvider
{

    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $order_property
     *
     * @return RecordIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->getOpenCourseService()->getOpenCourses(
            $this->get_component()->getUser(),
            $condition,
            $offset,
            $count,
            $order_property);
    }

    /**
     * Counts the data
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getOpenCourseService()->countOpenCourses($this->get_component()->getUser(), $condition);
    }

    /**
     *
     * @return OpenCourseService
     */
    public function getOpenCourseService()
    {
        return $this->get_component()->getOpenCourseService();
    }
}
