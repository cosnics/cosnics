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
    protected $openForStudents = false;

    /**
     * EvaluationConfiguration constructor.
     *
     * @param int $entityType
     * @param bool $openForStudents
     */
    public function __construct(int $entityType = 0, bool $openForStudents = false)
    {
        $this->entityType = $entityType;
        $this->openForStudents = $openForStudents;
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
    public function getOpenForStudents(): bool
    {
        return $this->openForStudents;
    }

    /**
     * @param bool $openForStudents
     */
    public function setOpenForStudents(bool $openForStudents): void
    {
        $this->openForStudents = $openForStudents;
    }
}