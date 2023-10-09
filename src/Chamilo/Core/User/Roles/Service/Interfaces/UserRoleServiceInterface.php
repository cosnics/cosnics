<?php
namespace Chamilo\Core\User\Roles\Service\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Manages roles
 *
 * @package Chamilo\Core\User\Roles\Service\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface UserRoleServiceInterface
{

    public function addRoleForUser(User $user, string $roleName): void;

    /**
     * @param \Chamilo\Core\User\Roles\Storage\DataClass\Role[] $rolesToMatch
     */
    public function doesUserHaveAtLeastOneRole(User $user, array $rolesToMatch = []): bool;

    /**
     * @return \Chamilo\Core\User\Roles\Storage\DataClass\Role[]
     */
    public function getRolesForUser(User $user): array;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    public function getUsersForRole(string $roleName): ArrayCollection;

    public function removeRoleFromUser(User $user, string $roleName): void;
}