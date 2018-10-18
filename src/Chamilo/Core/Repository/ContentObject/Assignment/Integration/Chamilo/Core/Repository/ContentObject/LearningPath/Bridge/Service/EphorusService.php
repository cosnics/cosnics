<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\EphorusRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EphorusService extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\EphorusService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\EphorusRepository
     */
    protected $ephorusRepository;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequestsByTreeNodeData(TreeNodeData $treeNodeData, Condition $condition = null)
    {
        return $this->ephorusRepository->countAssignmentEntriesWithRequestsByTreeNodeData($treeNodeData, $condition);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequestsByTreeNodeData(TreeNodeData $treeNodeData, RecordRetrievesParameters $recordRetrievesParameters = null)
    {
        return $this->ephorusRepository->findAssignmentEntriesWithRequestsByTreeNodeData($treeNodeData, $recordRetrievesParameters);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntriesByTreeNodeData(TreeNodeData $treeNodeData, array $entryIds = [])
    {
        if(empty($entryIds))
        {
            return [];
        }

        return $this->ephorusRepository->findEphorusRequestsForAssignmentEntriesByTreeNodeData($treeNodeData, $entryIds);
    }

}