<?php
namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EphorusService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EphorusServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\StorageParameters;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusServiceBridge implements EphorusServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var bool
     */
    protected $ephorusEnabled;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EphorusService
     */
    protected $ephorusService;

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
            $this->contentObjectPublication, $treeNodeData, $condition
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\StorageParameters $storageParameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
        TreeNodeData $treeNodeData, StorageParameters $storageParameters = new StorageParameters()
    )
    {
        return $this->ephorusService->findAssignmentEntriesWithEphorusRequestsByTreeNodeData(
            $this->contentObjectPublication, $treeNodeData, $storageParameters
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
        return $this->ephorusService->findEphorusRequestsForAssignmentEntriesByTreeNodeData(
            $this->contentObjectPublication, $treeNodeData, $entryIds
        );
    }

    /**
     * @return bool
     */
    public function isEphorusEnabled()
    {
        return $this->ephorusEnabled;
    }

    /**
     * @param bool $ephorusEnabled
     */
    public function setEphorusEnabled($ephorusEnabled)
    {
        $this->ephorusEnabled = $ephorusEnabled;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }
}
