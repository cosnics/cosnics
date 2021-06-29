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
     * @return int|null
     */
    public function getCurrentEntityIdentifier(User $currentUser): ?int;

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
     * @param int $entityId
     *
     * @return User[]
     */
    public function getUsersForEntity(int $entityId): array;

    /**
     * @param User $user
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityType, int $entityId): bool;

    /**
     * @param int $entityId
     * @return string
     */
    public function getEntityDisplayName(int $entityId): string;
}