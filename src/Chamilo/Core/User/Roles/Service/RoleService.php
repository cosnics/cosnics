<?php
namespace Chamilo\Core\User\Roles\Service;

use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Roles\Storage\Repository\Interfaces\RoleRepositoryInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Exception;

/**
 * Manages roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RoleService implements RoleServiceInterface
{

    /**
     *
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
     * Counts the roles
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function countRoles(Condition $condition = null)
    {
        return $this->roleRepository->countRoles($condition);
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

        if (!$this->roleRepository->create($role))
        {
            throw new Exception('The role with name ' . $roleName . ' could not be created');
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
        if (!$this->roleRepository->delete($role))
        {
            $roleName = $role->getRole();
            throw new Exception('The role with name ' . $roleName . ' could not be deleted');
        }
    }

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return $this->getService(
            DataClassRepositoryCache::class
        );
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
        catch (Exception $ex)
        {
            $role = $this->createRoleByName($roleName);
            $this->getDataClassRepositoryCache()->truncate(Role::class);

            return $role;
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
        if (!$role instanceof Role)
        {
            throw new Exception('Role not found by given name ' . $roleName);
        }

        return $role;
    }

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
    public function getRoles(Condition $condition = null, $count = null, $offset = null, $orderBy = [])
    {
        return $this->roleRepository->findRoles($condition, $count, $offset, $orderBy);
    }

    /**
     * @param string $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }
}