<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

interface LearningPathEvaluationServiceBridgeInterface
{
    /**
     * @param int $stepId
     * @return ContextIdentifier
     */
    public function getContextIdentifier(int $stepId): ContextIdentifier;

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool;

    /**
     * @param int $entityType
     * @return int[]
     */
    public function getTargetEntityIds(int $entityType): array;

    /**
     * @param int $entityType
     * @param int $entityId
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId): array;

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool;

    /**
     * @param User $currentUser
     * @param int $entityType
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser, int $entityType): ?int;

    /**
     * @param User $currentUser
     * @param int $entityType
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser, int $entityType);

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId);

    /**
     * @param int $entityType
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityType, int $entityId): string;

    /**
     * @param integer $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType);

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType);
}
