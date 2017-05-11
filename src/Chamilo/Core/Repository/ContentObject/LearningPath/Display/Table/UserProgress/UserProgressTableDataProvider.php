<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Format\Table\TableDataProvider;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\StaticColumnConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use JMS\Serializer\Tests\Fixtures\Order;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressTableDataProvider extends RecordTableDataProvider
{
    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $order_property
     *
     * @return ResultSet | RecordIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $this->cleanupOrderProperty($order_property);

        return $this->getLearningPathTrackingService()->getLearningPathAttemptsWithUser(
            $this->getLearningPath(), $this->getCurrentLearningPathTreeNode(),
            $condition, $offset, $count, $order_property
        );
    }

    /**
     * Counts the data
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getLearningPathTrackingService()->countLearningPathAttemptsWithUsers(
            $this->getLearningPath(), $condition
        );
    }

    /**
     * @return LearningPathTrackingService
     */
    protected function getLearningPathTrackingService()
    {
        return $this->get_component()->getLearningPathTrackingService();
    }

    /**
     * @return LearningPath
     */
    protected function getLearningPath()
    {
        return $this->get_component()->get_root_content_object();
    }

    /**
     * @return LearningPathTreeNode
     */
    protected function getCurrentLearningPathTreeNode()
    {
        return $this->get_component()->getCurrentLearningPathTreeNode();
    }

    /**
     * Cleans up the order property by passing the 'completed' and 'started' field to the nodes_completed counter
     *
     * @param array $order_property
     */
    protected function cleanupOrderProperty($order_property)
    {
        $firstOrderProperty = $order_property[0];

        if (
            $firstOrderProperty instanceof OrderBy &&
            $firstOrderProperty->get_property() instanceof StaticColumnConditionVariable
        )
        {
            $value = $firstOrderProperty->get_property()->get_value();

            if (in_array($value, array('progress', 'completed', 'started')))
            {
                $firstOrderProperty->get_property()->set_value('nodes_completed');
                $firstOrderProperty->set_direction(
                    $value == 'started' ? $firstOrderProperty->get_direction()
                        : ($firstOrderProperty->get_direction() == SORT_ASC ? SORT_DESC : SORT_ASC)
                );
            }
        }
    }
}