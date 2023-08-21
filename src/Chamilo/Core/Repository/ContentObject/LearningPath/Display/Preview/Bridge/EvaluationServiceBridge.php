<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\LearningPathEvaluationServiceBridgeInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;

class EvaluationServiceBridge implements LearningPathEvaluationServiceBridgeInterface
{
    /**
     * @param int $stepId
     * @return ContextIdentifier
     */
    public function getContextIdentifier(int $stepId): ContextIdentifier
    {
        return new ContextIdentifier('preview', 0);
    }

    /**
     * @return bool
     */
    public function canEditEvaluation(): bool
    {
        return true;
    }

    /**
     * @param int $entityType
     * @return int[]
     */
    public function getTargetEntityIds(int $entityType): array
    {
        return [];
    }

    /**
     * @param int $entityType
     * @param int $entityId
     * @return User[]
     */
    public function getUsersForEntity(int $entityType, int $entityId): array
    {
        return [];
    }

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool
    {
        return false;
    }

    /**
     * @param User $currentUser
     * @param int $entityType
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser, int $entityType): ?int
    {
        return 0;
    }

    /**
     * @param User $currentUser
     * @param int $entityType
     *
     * @return int[]
     */
    public function getAvailableEntityIdentifiersForUser(User $currentUser, int $entityType)
    {
        return [];
    }

    /**
     * @param int $entityType
     * @param int $entityId
     *
     * @return String
     */
    public function renderEntityNameByEntityTypeAndEntityId($entityType, $entityId)
    {
        return 'demo';
    }

    /**
     * @param int $entityType
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityType, int $entityId): string
    {
        return 'preview';
    }

    /**
     * @param integer $entityType
     *
     * @return string
     */
    public function getPluralEntityNameByType($entityType)
    {
        return 'preview';
    }

    /**
     * @param $entityType
     *
     * @return mixed
     */
    public function getEntityNameByType($entityType)
    {
        return 'User';
    }

    /**
     * @return bool
     */
    public function canUseAns(): bool
    {
        return false;
    }
}
