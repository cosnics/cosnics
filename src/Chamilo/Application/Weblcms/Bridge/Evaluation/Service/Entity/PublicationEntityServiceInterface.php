<?php
namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Service\Entity
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
interface PublicationEntityServiceInterface
{
    /**
     * @return array
     */
    public function getTargetEntityIds(): array;

    /**
     * @param int $entityId
     * @return array
     */
    public function getUsersForEntity(int $entityId): array;

    /**
     * @param User $user
     * @param int $entityId
     * @return bool
     */
    public function isUserPartOfEntity(User $user, int $entityId): bool;

    /**
     * @param User $currentUser
     *
     * @return int
     */
    public function getCurrentEntityIdentifier(User $currentUser): int;
}