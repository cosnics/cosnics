<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\Activity;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActivityService;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
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
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getActivityService()->countActivitiesForTreeNode($this->getCurrentTreeNode());
    }

    /**
     * @return ActivityService
     */
    protected function getActivityService()
    {
        return $this->get_component()->getService(ActivityService::class);
    }

    /**
     * @return TreeNode
     */
    protected function getCurrentTreeNode()
    {
        return $this->get_component()->getCurrentTreeNode();
    }

    /**
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param null $order_property
     *
     * @return \ArrayIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return new DataClassIterator(
            Activity::class, $this->getActivityService()->retrieveActivitiesForTreeNode(
            $this->getCurrentTreeNode(), $offset, $count, $order_property[0]
        )
        );
    }
}