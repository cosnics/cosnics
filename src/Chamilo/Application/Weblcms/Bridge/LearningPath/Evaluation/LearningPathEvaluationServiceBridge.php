<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Evaluation;

use Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity\PublicationEntityServiceManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathStepContextService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Evaluation
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathEvaluationServiceBridge implements LearningPathEvaluationServiceBridgeInterface
{
    /**
     * @var LearningPathStepContextService
     */
    protected $learningPathStepContextService;

    /**
     * @var PublicationEntityServiceManager
     */
    protected $publicationEntityServiceManager;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var bool
     */
    protected $canEditEvaluation;

    public function __construct(LearningPathStepContextService $learningPathStepContextService, PublicationEntityServiceManager $publicationEntityServiceManager)
    {
        $this->learningPathStepContextService = $learningPathStepContextService;
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
    }

    /**
     * @param ContentObjectPublication $publication
     */
    public function setContentObjectPublication(ContentObjectPublication $publication)
    {
        $this->contentObjectPublication = $publication;
    }

    /**
     * @param int $stepId
     * @return ContextIdentifier
     */
    public function getContextIdentifier(int $stepId): ContextIdentifier
    {
        $publicationClass = ContentObjectPublication::class_name();
        $publicationId = $this->contentObjectPublication->getId();
        $learningPathStepContext = $this->learningPathStepContextService->getOrCreateLearningPathStepContext($stepId, $publicationClass, $publicationId);
        return new ContextIdentifier(get_class($learningPathStepContext), $learningPathStepContext->getId());
    }

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool
    {
        return $this->canEditEvaluation;
    }

    /**
     * @param bool $canEditEvaluation
     */
    public function setCanEditEvaluation($canEditEvaluation = true)
    {
        $this->canEditEvaluation = $canEditEvaluation;
    }

    /**
     * @param int $entityType
     * @return int[]
     */
    public function getTargetEntityIds(int $entityType): array
    {
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        return $publicationEntityService->getTargetEntityIds();
    }

    /**
     * @param int $entityType
     * @param int $entityId
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId): array
    {
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        return $publicationEntityService->getUsersForEntity($entityId);
    }

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool
    {
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        return $publicationEntityService->isUserPartOfEntity($user, $entityId);
    }

    /**
     * @param User $currentUser
     * @param int $entityType
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser, int $entityType): ?int
    {
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        return $publicationEntityService->getCurrentEntityIdentifier($currentUser);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityType, int $entityId): string
    {
        $publicationEntityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);
        return $publicationEntityService->getEntityDisplayName($entityId);
    }
}