<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Unsubscribed;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * * *************************************************************************** Data provider for an unsubscribed
 * course user browser table.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class UnsubscribedUserTableDataProvider extends DataClassTableDataProvider
{
    
    // **************************************************************************
    // GENERAL FUNCTIONS
    // **************************************************************************
    /**
     * Gets the users
     * 
     * @param $offset int
     * @param $max_objects int
     * @param $order_by string
     * @return ResultSet A set of matching users.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_users_not_subscribed_to_course(
            $this->get_component()->get_course_id(), 
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
        return DataManager::count_users_not_subscribed_to_course(
            $this->get_component()->get_course_id(), 
            $condition);
    }
}
