<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\StaticColumnConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserProgressTableDataProvider extends RecordTableDataProvider
{

    /**
     * Cleans up the order property by passing the 'completed' and 'started' field to the nodes_completed counter
     *
     * @param array $order_property
     */
    protected function cleanupOrderProperty(?OrderBy $orderBy = null)
    {
        $firstOrderProperty = $orderBy->getFirst();

        if ($firstOrderProperty instanceof OrderProperty &&
            $firstOrderProperty->getConditionVariable() instanceof StaticConditionVariable)
        {
            $value = $firstOrderProperty->getConditionVariable()->getValue();

            if (in_array($value, array('progress', 'completed', 'started')))
            {
                $firstOrderProperty->getConditionVariable()->setValue('nodes_completed');
                $firstOrderProperty->setDirection(
                    $value == 'started' ? $firstOrderProperty->getDirection() :
                        ($firstOrderProperty->getDirection() == SORT_ASC ? SORT_DESC : SORT_ASC)
                );
            }
        }
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
        return $this->getTrackingService()->countLearningPathAttemptsWithUsers(
            $this->getLearningPath(), $this->getCurrentTreeNode(), $condition
        );
    }

    /**
     *
     * @return TreeNode
     */
    protected function getCurrentTreeNode()
    {
        return $this->get_component()->getCurrentTreeNode();
    }

    /**
     *
     * @return LearningPath
     */
    protected function getLearningPath()
    {
        return $this->get_component()->get_root_content_object();
    }

    /**
     *
     * @return TrackingService
     */
    protected function getTrackingService()
    {
        return $this->get_component()->getTrackingService();
    }

    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $this->cleanupOrderProperty($order_property);

        return $this->getTrackingService()->getLearningPathAttemptsWithUser(
            $this->getLearningPath(), $this->getCurrentTreeNode(), $condition, $offset, $count, $order_property
        );
    }
}