<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table\TreeNodeAttempt;

use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\TableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * Shows the progress of some tree nodes for a user in the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeAttemptTableDataProvider extends TableDataProvider
{

    /**
     *
     * @var array
     */
    protected $data;

    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $order_property
     *
     * @return ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return new ArrayResultSet(array_slice($this->getAllData(), $offset, $count));
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
        return count($this->getAllData());
    }

    /**
     * Retrieves, caches and returns the data
     *
     * @return array
     */
    protected function getAllData()
    {
        if (! isset($this->data))
        {
            $treeNode = $this->get_component()->getCurrentTreeNode();

            /** @var LearningPath $learningPath */
            $learningPath = $this->get_component()->get_root_content_object();

            /** @var User $user */
            $user = $this->get_component()->getReportingUser();

            /** @var TrackingService $trackingService */
            $trackingService = $this->get_component()->getTrackingService();

            $this->data = array_values(
                $trackingService->getTreeNodeAttempts($learningPath, $user, $treeNode));
        }

        return $this->data;
    }
}