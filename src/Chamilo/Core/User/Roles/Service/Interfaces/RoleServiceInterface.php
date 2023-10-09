<?php
namespace Chamilo\Core\User\Roles\Service\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Manages roles
 *
 * @package Chamilo\Core\User\Roles\Service\Interfaces
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface RoleServiceInterface
{

    public function countRoles(?Condition $condition = null): int;

    public function createRoleByName(string $roleName): Role;

    public function deleteRole(Role $role);

    public function getOrCreateRoleByName(string $roleName): Role;

    public function getRoleByName(string $roleName): Role;

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
}