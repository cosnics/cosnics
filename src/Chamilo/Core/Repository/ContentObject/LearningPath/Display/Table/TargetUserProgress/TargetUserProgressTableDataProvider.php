<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TargetUserProgress;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\UserProgress\UserProgressTableDataProvider;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TargetUserProgressTableDataProvider extends UserProgressTableDataProvider
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

        return $this->getLearningPathTrackingService()->getTargetUsersWithLearningPathAttempts(
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
        return $this->getLearningPathTrackingService()->countTargetUsersWithLearningPathAttempts(
            $this->getLearningPath(), $condition
        );
    }
}