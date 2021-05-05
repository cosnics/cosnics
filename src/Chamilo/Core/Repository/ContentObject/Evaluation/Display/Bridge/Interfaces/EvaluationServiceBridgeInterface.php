<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Interfaces
 * @author Stefan Gabriëls <stefan.gabriels@hogent.be>
 */
interface EvaluationServiceBridgeInterface
{
    /**
     * @param User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser): int;

    /**
     * @return integer
     */
    public function getCurrentEntityType(): int;

    /**
     * @return ContextIdentifier
     */
    public function getContextIdentifier(): ContextIdentifier;

    /**
     * @return boolean
     */
    public function canEditEvaluation(): bool;

    /**
     * @return boolean
     */
    public function getReleaseScores(): bool;

    /**
     * @return int[]
     */
    public function getTargetEntityIds(): array;

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId): array;

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool;

    /**
     * @param int $evaluationId
     * @param int $entityId
     * @return EvaluationEntry
     */
    public function createEvaluationEntryIfNotExists(int $evaluationId, int $entityId): EvaluationEntry;

    /**
     * @param int $evaluationId
     * @param int $userId
     * @param int $entityId
     * @param int $score
     *
     * @return EvaluationEntryScore
     */
    public function saveEntryScoreForEntity(int $evaluationId, int $userId, int $entityId, int $score): EvaluationEntryScore;

    /**
     * @param int $evaluationId
     * @param int $userId
     * @param int $entityId
     *
     * @return EvaluationEntryScore
     */
    public function saveEntityAsPresent(int $evaluationId, int $userId, int $entityId): EvaluationEntryScore;

    /**
     * @param int $evaluationId
     * @param int $userId
     * @param int $entityId
     *
     * @return EvaluationEntryScore
     */
    public function saveEntityAsAbsent(int $evaluationId, int $userId, int $entityId): EvaluationEntryScore;
}