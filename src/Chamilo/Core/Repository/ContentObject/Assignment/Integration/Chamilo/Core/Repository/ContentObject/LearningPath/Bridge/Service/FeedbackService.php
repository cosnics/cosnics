<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class FeedbackService extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Service\FeedbackService
{
    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository
     */
    protected $feedbackRepository;

    /**
     * AssignmentService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        parent::__construct($feedbackRepository);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeDataAndEntityType(TreeNodeData $treeNodeData, $entityType)
    {
        return $this->feedbackRepository->countDistinctFeedbackByTreeNodeDataAndEntityType(
            $treeNodeData,
            $entityType
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
        TreeNodeData $treeNodeData,
        $entityType, $entityId
    )
    {
        return $this->feedbackRepository->countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
            $treeNodeData,
            $entityType,
            $entityId
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType,
        $entityId
    )
    {
        return $this->feedbackRepository->countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
            $treeNodeData,
            $entityType,
            $entityId
        );
    }
}