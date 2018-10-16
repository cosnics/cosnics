<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface EphorusServiceBridgeInterface
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface EphorusServiceBridgeInterface
{

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequestsByTreeNodeData(TreeNodeData $treeNodeData, Condition $condition = null);

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
        TreeNodeData $treeNodeData, RecordRetrievesParameters $recordRetrievesParameters = null
    );

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntriesByTreeNodeData(TreeNodeData $treeNodeData, array $entryIds = []);

    /**
     * @return bool
     */
    public function isEphorusEnabled();
}