<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Activity;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActivityService;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

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
     * @return ArrayResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return new ArrayResultSet(
            $this->getActivityService()->retrieveActivitiesForTreeNode(
                $this->getCurrentTreeNode(), $offset, $count, $order_property[0]
            )
        );
    }

    /**
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getActivityService()->countActivitiesForTreeNode($this->getCurrentTreeNode());
    }

    /**
     * @return TreeNode
     */
    protected function getCurrentTreeNode()
    {
        return $this->get_component()->getCurrentTreeNode();
    }

    /**
     * @return ActivityService
     */
    protected function getActivityService()
    {
        return $this->get_component()->getService(ActivityService::class);
    }
}