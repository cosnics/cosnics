<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class SubscribedUserTableDataProvider extends RecordTableDataProvider
{

    /**
     * Gets the users
     *
     * @param $offset int
     * @param $count int
     * @param $order_property string
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator A set of matching learning objects.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_course_group_users_with_subscription_time(
            $this->get_component()->getCurrentCourseGroup()->get_id(),
            $condition,
            $offset,
            $count,
            $order_property);
    }

    /**
     * Gets the number of users in the table
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count_course_group_users(
            $this->get_component()->getCurrentCourseGroup()->get_id(),
            $condition);
    }
}
