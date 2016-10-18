<?php
namespace Chamilo\Core\User\Roles\Storage\Repository\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;

/**
 * Repository to manage the data of roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface RoleRepositoryInterface
{
    /**
     * Returns a role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function findRoleByName($roleName);
}