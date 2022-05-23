<?php
namespace Chamilo\Core\User\Roles\Service\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

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

    /**
     * Retrieves the roles
     * 
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param array $orderBy
     *
     * @return Role[]
     */
    public function getRoles(Condition $condition = null, $count = null, $offset = null, $orderBy = null);

    /**
     * Counts the roles
     * 
     * @param Condition $condition
     *
     * @return int
     */
    public function countRoles(Condition $condition = null);
}