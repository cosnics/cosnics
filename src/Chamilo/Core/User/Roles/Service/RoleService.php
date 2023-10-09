<?php
namespace Chamilo\Core\User\Roles\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * Manages roles
 *
 * @package Chamilo\Core\User\Roles\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class RoleService implements RoleServiceInterface
{

    protected RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function countRoles(?Condition $condition = null): int
    {
        return $this->getRoleRepository()->countRoles($condition);
    }

    /**
     * @throws \Exception
     */
    public function createRoleByName(string $roleName): Role
    {
        $role = new Role();
        $role->setRole($roleName);

        if (!$this->getRoleRepository()->createRole($role))
        {
            throw new Exception('The role with name ' . $roleName . ' could not be created');
        }

        return $role;
    }

    /**
     * @throws \Exception
     */
    public function deleteRole(Role $role): bool
    {
        if (!$this->getRoleRepository()->deleteRole($role))
        {
            throw new Exception('The role with name ' . $role->getRole() . ' could not be deleted');
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function getOrCreateRoleByName(string $roleName): Role
    {
        try
        {
            return $this->getRoleByName($roleName);
        }
        catch (Exception)
        {
            return $this->createRoleByName($roleName);
        }
    }

    /**
     * @throws \Exception
     */
    public function getRoleByName(string $roleName): Role
    {
        $role = $this->getRoleRepository()->findRoleByName($roleName);

        if (!$role instanceof Role)
        {
            throw new Exception('Role not found by given name ' . $roleName);
        }

        return $role;
    }

    public function getRoleRepository(): RoleRepositoryInterface
    {
        return $this->roleRepository;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Roles\Storage\DataClass\Role>
     */
    public function getRoles(
        ?Condition $condition = null, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getRoleRepository()->findRoles($condition, $count, $offset, $orderBy);
    }
}