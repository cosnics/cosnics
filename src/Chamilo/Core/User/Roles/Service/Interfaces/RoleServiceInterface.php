<?php
namespace Chamilo\Core\User\Roles\Service\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;

/**
 * Manages roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface RoleServiceInterface
{
    /**
     * Creates a role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     *
     * @throws \Exception
     */
    public function createRoleByName($roleName);

    /**
     * Deletes a given role
     *
     * @param Role $role
     *
     * @throws \Exception
     */
    public function deleteRole(Role $role);

    /**
     * Returns a role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     *
     * @throws \Exception
     */
    public function getRoleByName($roleName);

    /**
     * Either retrieves or creates a new role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function getOrCreateRoleByName($roleName);
}