<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNodeConfigurationInterface;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EvaluationConfiguration implements TreeNodeConfigurationInterface
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $entityType = 0;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $releaseScores = false;

    /**
     * EvaluationConfiguration constructor.
     *
     * @param int $entityType
     * @param bool $releaseScores
     */
    public function __construct(int $entityType = 0, bool $releaseScores = false)
    {
        $this->entityType = $entityType;
        $this->releaseScores = $releaseScores;
    }

    /**
     * @return int
     */
    public function getEntityType(): int
    {
        return $this->entityType;
    }

    /**
     * @param int $entityType
     */
    public function setEntityType(int $entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return bool
     */
    public function getReleaseScores(): bool
    {
        return $this->releaseScores;
    }

    /**
     * @param bool $releaseScores
     */
    public function setReleaseScores(bool $releaseScores): void
    {
        $this->releaseScores = $releaseScores;
    }
}