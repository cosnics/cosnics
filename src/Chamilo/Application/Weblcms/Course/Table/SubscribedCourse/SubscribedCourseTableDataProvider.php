<?php
namespace Chamilo\Application\Weblcms\Course\Table\SubscribedCourse;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class describes a data provider for the subscribed course table
 *
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubscribedCourseTableDataProvider extends RecordTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_user_courses($this->get_component()->get_user(), $condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieve_users_courses_with_course_type(
            $this->get_component()->get_user(), $condition, $offset, $count, $orderBy
        );
    }
}
