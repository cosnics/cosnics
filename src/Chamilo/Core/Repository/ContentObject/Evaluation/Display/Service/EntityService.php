<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\EntityRepository;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScoreTargetUser;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EntityService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\EntityRepository
     */
    protected $entityRepository;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    public function __construct(EntityRepository $entityRepository, UserService $userService)
    {
        $this->entityRepository = $entityRepository;
        $this->userService = $userService;
    }

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getUsersFromIDs(array $userIds, FilterParameters $filterParameters)
    {
        return $this->entityRepository->getUsersFromIDs($userIds, $filterParameters);
    }

    public function getUserEntity(ContextIdentifier $contextIdentifier, int $entityType, int $entityId)
    {
        return $this->entityRepository->getUserEntity($contextIdentifier->getContextClass(), $contextIdentifier->getContextId(), $entityType, $entityId);
    }

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countUsersFromIDs(array $userIds, FilterParameters $filterParameters)
    {
        return $this->entityRepository->countUsersFromIDs($userIds, $filterParameters);
    }

    /**
     * @param int $entityId
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUserForEntity($entityId)
    {
        return $this->userService->findUserByIdentifier($entityId);
    }


    /**
     * @param ContextIdentifier $contextIdentifier
     * @param int $entityType
     * @param int $entityId
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     */
    public function getEvaluationEntryForEntity(ContextIdentifier $contextIdentifier, int $entityType, int $entityId)
    {
        return $this->entityRepository->getEvaluationEntry($contextIdentifier, $entityType, $entityId);
    }

    /**
     * @param int $entryId
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     */
    public function getEvaluationEntryScore(int $entryId)
    {
        return $this->entityRepository->getEvaluationEntryScore($entryId);
    }

    /**
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param ContextIdentifier $contextIdentifier
     * @param int $entityType
     * @param int $entityId
     * @param string $score
     */
    public function createOrUpdateEvaluationEntryScoreForEntity(int $evaluationId, int $evaluatorId, ContextIdentifier $contextIdentifier, int $entityType, int $entityId, string $score): void
    {
        $evaluationEntry = $this->entityRepository->getEvaluationEntry($contextIdentifier, $entityType, $entityId) ?:
            $this->createEvaluationEntry($evaluationId, $contextIdentifier, $entityType, $entityId);

        $evaluationEntryScore = $this->entityRepository->getEvaluationEntryScore($evaluationEntry->getId());

        if ($evaluationEntryScore instanceof EvaluationEntryScore) {
            $evaluationEntryScore->setScore($score);
            $this->entityRepository->updateEvaluationEntryScore($evaluationEntryScore);
        } else {
            $evaluationEntryScore = $this->createEvaluationEntryScore($evaluationEntry->getId(), $evaluatorId, $score);
            $this->createEvaluationTargetUser($entityId, $evaluationEntryScore->getId());
        }
    }

    /**
     * @param int $evaluationId
     * @param ContextIdentifier $contextIdentifier
     * @param int $entityType
     * @param int $entityId
     *
     * @return EvaluationEntry
     */
    private function createEvaluationEntry(int $evaluationId, ContextIdentifier $contextIdentifier, int $entityType, int $entityId): EvaluationEntry
    {
        $evaluationEntry = new EvaluationEntry();
        $evaluationEntry->setEvaluationId($evaluationId);
        $evaluationEntry->setContextClass($contextIdentifier->getContextClass());
        $evaluationEntry->setContextId($contextIdentifier->getContextId());
        $evaluationEntry->setEntityType($entityType);
        $evaluationEntry->setEntitityId($entityId);
        $this->entityRepository->createEvaluationEntry($evaluationEntry);

        return $evaluationEntry;
    }

    /**
     * @param int $entryId
     * @param int $evaluatorId
     * @param string $score
     *
     * @return EvaluationEntryScore
     */
    private function createEvaluationEntryScore(int $entryId, int $evaluatorId, string $score): EvaluationEntryScore
    {
        $evaluationEntryScore = new EvaluationEntryScore();
        $evaluationEntryScore->setEvaluatorId($evaluatorId);
        $evaluationEntryScore->setEntryId($entryId);
        $evaluationEntryScore->setScore($score);
        $evaluationEntryScore->setCreatedTime(time());
        $this->entityRepository->createEvaluationEntryScore($evaluationEntryScore);

        return $evaluationEntryScore;
    }

    /**
     * @param int $entityId
     * @param int $entryScoreId
     *
     * @return EvaluationEntryScoreTargetUser
     */
    private function createEvaluationTargetUser(int $entityId, int $entryScoreId): EvaluationEntryScoreTargetUser
    {
        $targetUser = new EvaluationEntryScoreTargetUser();
        $targetUser->setTargetUserId($entityId);
        $targetUser->setScoreId($entryScoreId);
        $this->entityRepository->createEvaluationEntryScoreTargetUser($targetUser);

        return $targetUser;
    }
}