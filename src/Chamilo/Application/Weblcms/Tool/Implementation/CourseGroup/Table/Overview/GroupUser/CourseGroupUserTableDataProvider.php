<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * * ***************************************************************************
 * Data provider for an all subscribed course_group user browser table, including users
 * subscribed through (sub-)groups.
 * ****************************************************************************
 */
class CourseGroupUserTableDataProvider extends RecordTableDataProvider
{
    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_course_group_users($this->get_component()->get_table_course_group_id(), $condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return DataManager::retrieve_course_group_users_with_subscription_time(
            $this->get_component()->get_table_course_group_id(), $condition, $offset, $count, $orderBy
        );
    }
}
