<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\ActivityTable;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Table data provider for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActivityTableDataProvider
    extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Table\Activity\ActivityTableDataProvider
{
    /**
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param null $order_property
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ArrayResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_activities(
            $this->get_component()->get_current_content_object(),
            $condition,
            $offset,
            $count,
            $order_property
        );
    }

    /**
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager::count_activities($this->get_component()->get_current_content_object(), $condition);
    }
}