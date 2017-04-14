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
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

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
        return $this->getLearningPathTrackingService()->getLearningPathAttemptsWithUser(
            $this->getLearningPath(), $condition, $offset, $count, $order_property
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
}