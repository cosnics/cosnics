<?php
namespace Chamilo\Application\Weblcms\Course\Table\CourseTable;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * This class describes a data provider for the course table
 *
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTableDataProvider extends RecordTableDataProvider
{
    
    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_courses_with_course_type($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return DataManager::retrieve_courses_with_course_type($condition, $offset, $count, $orderBy);
    }
}
