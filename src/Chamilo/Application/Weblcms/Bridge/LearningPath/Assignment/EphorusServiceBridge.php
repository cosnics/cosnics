<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EphorusService;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusServiceBridge implements EphorusServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EphorusService
     */
    protected $ephorusService;

    /**
     * @var bool
     */
    protected $ephorusEnabled;

    /**
     * EphorusServiceBridge constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EphorusService $ephorusService
     */
    public function __construct(EphorusService $ephorusService)
    {
        $this->ephorusService = $ephorusService;
    }

    /**
     * @param bool $ephorusEnabled
     */
    protected function setEphorusEnabled($ephorusEnabled)
    {
        $this->ephorusEnabled = $ephorusEnabled;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequestsByTreeNodeData(
        TreeNodeData $treeNodeData, Condition $condition = null
    )
    {
        return $this->ephorusService->countAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $treeNodeData, $condition
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
        TreeNodeData $treeNodeData, RecordRetrievesParameters $recordRetrievesParameters = null
    )
    {
        return $this->ephorusService->findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $treeNodeData, $recordRetrievesParameters
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntriesByTreeNodeData(
        TreeNodeData $treeNodeData, array $entryIds = []
    )
    {
        return $this->ephorusService->findEphorusRequestsForAssignmentEntriesByTreeNodeData($treeNodeData, $entryIds);
    }

    /**
     * @return bool
     */
    public function isEphorusEnabled()
    {
        return $this->ephorusEnabled;
    }
}
