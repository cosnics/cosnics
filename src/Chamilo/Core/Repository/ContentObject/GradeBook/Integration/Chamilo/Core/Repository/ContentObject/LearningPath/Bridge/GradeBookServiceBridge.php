<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\GradeBookItemScoreService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Display\Bridge\Interfaces\GradeBookServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Core\Repository\ContentObject\GradeBook\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathGradeBookServiceBridgeInterface;
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
     * GradeBookServiceBridge constructor.
     *
     * @param LearningPathGradeBookServiceBridgeInterface $learningPathGradeBookServiceBridge
     */
    public function __construct(LearningPathGradeBookServiceBridgeInterface $learningPathGradeBookServiceBridge)
    {
        $this->learningPathGradeBookServiceBridge = $learningPathGradeBookServiceBridge;
    }

    /*public function getContextIdentifier(): ContextIdentifier
    {
    }*/

    /**
     * @return bool
     */
    public function canEditGradeBook(): bool
    {
        return $this->learningPathGradeBookServiceBridge->canEditGradeBook();
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
     * @return array
     */
    public function findScores(GradeBookItem $gradeBookItem)
    {
        return $this->learningPathGradeBookServiceBridge->findScores($gradeBookItem);
    }
}