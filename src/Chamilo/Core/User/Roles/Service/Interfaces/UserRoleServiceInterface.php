<?php
namespace Chamilo\Core\User\Roles\Service\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Manages roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserRoleServiceInterface
{
    /**
     * Returns the roles for a given user
     *
     * @param User $user
     *
     * @return Role[]
     */
    public function getRolesForUser(User $user);

    /**
     * Returns the users that are attached to a given role
     *
     * @param string $roleName
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsersForRole($roleName);

    /**
     * Adds a role to a given user by a given role name
     *
     * @param User $user
     * @param string $roleName
     *
     * @throws \Exception
     */
    public function addRoleForUser(User $user, $roleName);

    /**
     * Removes a role by a given name from a given user
     *
     * @param User $user
     * @param string $roleName
     *
     * @throws \Exception
     */
    public function removeRoleFromUser(User $user, $roleName);

    /**
     * Checks whether or not a user matches one of the requested roles
     *
     * @param User $user
     * @param Role[] $rolesToMatch
     *
     * @return bool
     */
    public function doesUserHasAtLeastOneRole(User $user, $rolesToMatch = array());
}