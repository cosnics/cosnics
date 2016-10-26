<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * * ***************************************************************************
 * Data provider for an all subscribed course_group user browser table, including users
 * subscribed through (sub-)groups.
 * ****************************************************************************
 */
class CourseGroupUserTableDataProvider extends RecordTableDataProvider
{
    // **************************************************************************
    // GENERAL FUNCTIONS
    // **************************************************************************

    /**
     * Gets the users.
     *
     * @param int $offset
     * @param int $count
     * @param string $order_property
     *
     * @return \libraries\storage\ResultSet A set of matching users.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_course_group_users_with_subscription_time(
            $this->get_component()->get_table_course_group_id(),
            $condition,
            $offset,
            $count,
            $order_property);
    }

    /**
     * Gets the number of users.
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count_course_group_users($this->get_component()->get_table_course_group_id(), $condition);
    }
}
