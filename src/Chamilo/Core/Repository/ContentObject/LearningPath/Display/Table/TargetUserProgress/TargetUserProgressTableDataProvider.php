<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TargetUserProgress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress\UserProgressTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TargetUserProgressTableDataProvider extends UserProgressTableDataProvider
{

    /**
     * Counts the data
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getTrackingService()->countTargetUsersWithLearningPathAttempts(
            $this->getLearningPath(), $condition
        );
    }

    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $this->cleanupOrderProperty($order_property);

        return $this->getTrackingService()->getTargetUsersWithLearningPathAttempts(
            $this->getLearningPath(), $this->getCurrentTreeNode(), $condition, $offset, $count, $order_property
        );
    }
}