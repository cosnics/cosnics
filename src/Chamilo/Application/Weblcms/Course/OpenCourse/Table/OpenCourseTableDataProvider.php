<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Table;

use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DataProvider for open courses
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseTableDataProvider extends CourseTableDataProvider
{

    public function countData(?Condition $condition = null): int
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getOpenCourseService()->getOpenCourses(
            $this->get_component()->getUser(), $condition, $offset, $count, $orderBy
        );
    }
}
