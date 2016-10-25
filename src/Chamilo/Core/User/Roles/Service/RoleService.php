<?php

namespace Chamilo\Core\User\Roles\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface;
use Chamilo\Libraries\Storage\Cache\DataClassCache;

/**
 * Manages roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RoleService implements RoleServiceInterface
{
    /**
     * @var RoleRepositoryInterface
     */
    protected $roleRepository;

    /**
     * RoleService constructor.
     *
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Creates a role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     *
     * @throws \Exception
     */
    public function createRoleByName($roleName)
    {
        $role = new Role();
        $role->setRole($roleName);

        if (!$role->create())
        {
            throw new \Exception('The role with name ' . $roleName . ' could not be created');
        }

        return $role;
    }

    /**
     * Deletes a given role
     *
     * @param Role $role
     *
     * @throws \Exception
     */
    public function deleteRole(Role $role)
    {
        if(!$role->delete())
        {
            $roleName = $role->getRole();
            throw new \Exception('The role with name ' . $roleName . ' could not be deleted');
        }
    }

    /**
     * Returns a role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     *
     * @throws \Exception
     */
    public function getRoleByName($roleName)
    {
        $role = $this->roleRepository->findRoleByName($roleName);
        if(!$role instanceof Role)
        {
            throw new \Exception('Role not found by given name ' . $roleName);
        }

        return $role;
    }

    /**
     * Either retrieves or creates a new role by a given name
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function getOrCreateRoleByName($roleName)
    {
        try
        {
            return $this->getRoleByName($roleName);
        }
        catch(\Exception $ex)
        {
            $role = $this->createRoleByName($roleName);
            DataClassCache::truncate(Role::class_name());

            return $role;
        }
    }
}