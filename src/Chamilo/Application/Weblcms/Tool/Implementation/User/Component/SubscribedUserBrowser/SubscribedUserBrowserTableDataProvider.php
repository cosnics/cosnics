<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubscribedUserBrowser;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * Data provider for a direct subscribed course user browser table, or users
 * in a direct subscribed group.
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
class SubscribedUserBrowserTableDataProvider extends RecordTableDataProvider
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Gets the users
     * 
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator A set of matching users.
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_users_directly_subscribed_to_course(
            $condition, 
            $offset, 
            $count, 
            $order_property);
    }

    /**
     * Gets the number of users.
     * 
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count_users_directly_subscribed_to_course(
            $condition);
    }
}
