<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNodeConfigurationInterface;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentConfiguration implements TreeNodeConfigurationInterface
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
    protected $checkForPlagiarism = false;

    /**
     * AssignmentConfiguration constructor.
     *
     * @param int $entityType
     * @param bool $checkForPlagiarism
     */
    public function __construct(int $entityType = 0, bool $checkForPlagiarism = false)
    {
        $this->entityType = $entityType;
        $this->checkForPlagiarism = $checkForPlagiarism;
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
    public function getCheckForPlagiarism(): bool
    {
        return $this->checkForPlagiarism;
    }

    /**
     * @param bool $checkForPlagiarism
     */
    public function setCheckForPlagiarism(bool $checkForPlagiarism): void
    {
        $this->checkForPlagiarism = $checkForPlagiarism;
    }
}