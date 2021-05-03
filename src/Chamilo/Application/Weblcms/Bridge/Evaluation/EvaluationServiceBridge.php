<?php

namespace Chamilo\Application\Weblcms\Bridge\Evaluation;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\EvaluationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\EntityService;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationServiceBridge implements EvaluationServiceBridgeInterface
{
    /**
     * @var EntityService
     */
    protected $entityService;

    /**
     * @var integer
     */
    protected $publicationId;

    /**
     * @var integer
     */
    protected $currentEntityType;

    /**
     * @var ContextIdentifier
     */
    protected $contextIdentifier;

    /**
     * @var bool
     */
    protected $canEditEvaluation;

    /**
     * @var bool
     */
    protected $releaseScores;

    /**
     * @param EntityService $entityService
     */
    public function __construct(EntityService $entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * @param int $publicationId
     */
    public function setPublicationId(int $publicationId)
    {
        $this->publicationId = $publicationId;
    }

    /**
     * @param User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser): int
    {
        return $currentUser->getId(); // Todo: get according to entity type
    }

    /**
     * @return integer
     */
    public function getCurrentEntityType(): int
    {
        return $this->currentEntityType;
    }

    /**
     * @param integer $currentEntityType
     */
    public function setCurrentEntityType(int $currentEntityType)
    {
        $this->currentEntityType = $currentEntityType;
    }

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier
    {
        return $this->contextIdentifier;
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     */
    public function setContextIdentifier(ContextIdentifier $contextIdentifier)
    {
        $this->contextIdentifier = $contextIdentifier;
    }

    /**
     * @return boolean
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
     * @return boolean
     */
    public function getReleaseScores(): bool
    {
        return $this->releaseScores;
    }

    /**
     * @param bool $releaseScores
     */
    public function setReleaseScores($releaseScores)
    {
        $this->releaseScores = $releaseScores;
    }

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::getPublicationTargetUserIds($this->publicationId, null);
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId): array
    {
        // Todo: actually search by entity type
        return [$this->entityService->getUserForEntity($entityId)];
        /*$entityService = $this->entityServiceManager->getEntityServiceByType($this->getCurrentEntityType());

        return $entityService->getUsersForEntity($entityId);*/
    }

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool
    {
        if ($entityType == 0 && $user->getId() == $entityId)
        {
            return true;
        }
        // todo: all other cases
        return false;

        //return $this->entityService->isUserPartOfEntity($user, $this->contentObjectPublication, $entityId);
    }

    public function saveEntryScoreForEntity(int $evaluationId, int $userId, int $entityId, int $score): EvaluationEntryScore
    {
        return $this->entityService->createOrUpdateEvaluationEntryScoreForEntity($evaluationId, $userId, $this->contextIdentifier, $this->currentEntityType, $entityId, $score);
    }

    public function saveEntityAsPresent(int $evaluationId, int $userId, int $entityId): EvaluationEntryScore
    {
        return $this->entityService->saveEntityAsPresent($evaluationId, $userId, $this->contextIdentifier, $this->currentEntityType, $entityId);
    }

    public function saveEntityAsAbsent(int $evaluationId, int $userId, int $entityId): EvaluationEntryScore
    {
        return $this->entityService->saveEntityAsAbsent($evaluationId, $userId, $this->contextIdentifier, $this->currentEntityType, $entityId);
    }

}
