<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\DataClass\GradeBook;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathGradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * Class GradeBookServiceBridge
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge
 */
class GradeBookServiceBridge implements GradeBookServiceBridgeInterface
{
    /**
     * @var LearningPathGradeBookServiceBridgeInterface
     */
    protected $learningPathGradeBookServiceBridge;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * GradeBookServiceBridge constructor.
     *
     * @param LearningPathGradeBookServiceBridgeInterface $learningPathGradeBookServiceBridge
     */
    public function __construct(LearningPathGradeBookServiceBridgeInterface $learningPathGradeBookServiceBridge)
    {
        $this->learningPathGradeBookServiceBridge = $learningPathGradeBookServiceBridge;
    }

    /**
     * @param TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof GradeBook)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid gradebook and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }

    public function getContextIdentifier(): ContextIdentifier
    {
        return $this->learningPathGradeBookServiceBridge->getContextIdentifier($this->treeNode->getId());
    }

    /**
     * @return bool
     */
    public function canEditGradeBook(): bool
    {
        return $this->learningPathGradeBookServiceBridge->canEditGradeBook();
    }

    /**
     * @param FilterParameters|null $filterParameters
     * @return User[]
     */
    public function getTargetUsers(FilterParameters $filterParameters = null): array
    {
        return $this->learningPathGradeBookServiceBridge->getTargetUsers($filterParameters);
    }

    /**
     * @param FilterParameters|null $filterParameters
     * @return int[]
     */
    public function getTargetUserIds(FilterParameters $filterParameters = null): array
    {
        return $this->learningPathGradeBookServiceBridge->getTargetUserIds($filterParameters);
    }

    /**
     * @return string
     */
    public function getContextTitle(): string
    {
        return '';
    }

    /**
     * @return GradeBookItem[]
     */
    public function findPublicationGradeBookItems()
    {
        return $this->learningPathGradeBookServiceBridge->findPublicationGradeBookItems();
    }

    /**
     * @param GradeBookItem $gradeBookItem
     *
     * @return GradeScoreInterface[]
     */
    public function findScores(GradeBookItem $gradeBookItem)
    {
        return $this->learningPathGradeBookServiceBridge->findScores($gradeBookItem);
    }
}