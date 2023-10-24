<?php
namespace Chamilo\Core\User\Roles\Service\Interfaces;

use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Roles\Storage\DataClass\Role>
     */
    public function getRoles(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection;
}