<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\DirectSubscribedPlatformGroupBrowser;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * Data privider for a direct subscribed course group browser table.
 * 
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
class DirectSubscribedPlatformGroupBrowserTableDataProvider extends RecordTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_groups_directly_subscribed_to_course(
            $condition, 
            $offset, 
            $count, 
            $order_property);
    }

    /**
     * Counts the data
     * 
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count_groups_directly_subscribed_to_course(
            $condition);
    }
}
