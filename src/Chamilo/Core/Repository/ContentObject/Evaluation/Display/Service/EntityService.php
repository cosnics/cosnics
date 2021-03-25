<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository\EntityRepository;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScoreTargetUser;
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
     * @var \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository\EntityRepository
     */
    protected $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
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
     * @param int $evaluationId
     * @param int $evaluatorId
     * @param ContextIdentifier $contextIdentifier
     * @param int $entityId
     * @param string $score
     */
    public function createOrUpdateEvaluationEntryScoreForEntity(int $evaluationId, int $evaluatorId, ContextIdentifier $contextIdentifier, int $entityId, string $score): void
    {
        $evaluationEntry = $this->entityRepository->getEvaluationEntry($contextIdentifier, $entityId) ?:
            $this->createEvaluationEntry($evaluationId, $contextIdentifier, $entityId);

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
     * @param int $entityId
     *
     * @return EvaluationEntry
     */
    private function createEvaluationEntry(int $evaluationId, ContextIdentifier $contextIdentifier, int $entityId): EvaluationEntry
    {
        $evaluationEntry = new EvaluationEntry();
        $evaluationEntry->setEvaluationId($evaluationId);
        $evaluationEntry->setContextClass($contextIdentifier->getContextClass());
        $evaluationEntry->setContextId($contextIdentifier->getContextId());
        $evaluationEntry->setEntityType(1);
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