<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\AllSubscribed;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * Data provider for an all subscribed course user browser table, including users subscribed through (sub-)groups.
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
class AllSubscribedUserTableDataProvider extends RecordTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_all_course_users(
            $this->get_component()->get_course_id(), 
            $condition, 
            $offset, 
            $count, 
            $order_property);
    }

    /**
     * Counts the data
     * 
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count_all_course_users(
            $this->get_component()->get_course_id(), 
            $condition);
    }
}
