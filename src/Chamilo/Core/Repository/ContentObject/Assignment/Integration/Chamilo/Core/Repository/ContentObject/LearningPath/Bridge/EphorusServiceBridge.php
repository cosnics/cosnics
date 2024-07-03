<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use RuntimeException;

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
        Interfaces\EphorusServiceBridgeInterface $ephorusServiceBridgeInterface
    )
    {
        $this->ephorusServiceBridgeInterface = $ephorusServiceBridgeInterface;
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
     * @param \Chamilo\Libraries\Storage\Query\StorageParameters $dataClassParameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequests(
        StorageParameters $dataClassParameters = new StorageParameters()
    )
    {
        return $this->ephorusServiceBridgeInterface->findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $this->treeNode->getTreeNodeData(), $dataClassParameters
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

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assignment)
        {
            throw new RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }
}