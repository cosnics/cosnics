<?php
namespace Chamilo\Core\User\Roles\Storage\Repository\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Repository to manage the data for the relations between users and roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserRoleRepositoryInterface
{
    /**
     * Returns a user role relation for a given role id and user id
     *
     * @param int $roleId
     * @param int $userId
     *
     * @return Role
     */
    public function findUserRoleRelationByRoleAndUser($roleId, $userId);

    /**
     * Returns a list of roles for a user
     *
     * @param int $userId
     *
     * @return Role[]
     */
    public function findRolesForUser($userId);

    /**
     * Returns a list of users by a given role
     *
     * @param int $roleId
     *
     * @return User[]
     */
    public function findUsersForRole($roleId);
}