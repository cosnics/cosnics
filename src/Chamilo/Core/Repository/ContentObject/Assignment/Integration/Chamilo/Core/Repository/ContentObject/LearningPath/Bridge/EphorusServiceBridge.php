<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity\EntityTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entry\EntryTable;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusServiceBridge implements EphorusServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EphorusServiceBridgeInterface
     */
    protected $ephorusServiceBridgeInterface;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * EphorusServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EphorusServiceBridgeInterface $ephorusServiceBridgeInterface
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EphorusServiceBridgeInterface $ephorusServiceBridgeInterface
    )
    {
        $this->ephorusServiceBridgeInterface = $ephorusServiceBridgeInterface;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assignment)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }


    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithEphorusRequests(Condition $condition = null)
    {
        return $this->ephorusServiceBridgeInterface->countAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $this->treeNode->getTreeNodeData(), $condition
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequests(RecordRetrievesParameters $recordRetrievesParameters = null
    )
    {
        return $this->ephorusServiceBridgeInterface->findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $this->treeNode->getTreeNodeData(), $recordRetrievesParameters
        );
    }

    /**
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntries(array $entryIds = [])
    {
        return $this->ephorusServiceBridgeInterface->findEphorusRequestsForAssignmentEntriesByTreeNodeData(
            $this->treeNode->getTreeNodeData(), $entryIds
        );
    }

    /**
     * @return bool
     */
    public function isEphorusEnabled()
    {
        return $this->ephorusServiceBridgeInterface->isEphorusEnabled();
    }
}