<?php

namespace Chamilo\Core\Rights\Structure\Service;

use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationRoleServiceInterface;
use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationServiceInterface;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocationRole;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRoleRepositoryInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;

/**
 * Manages structure location roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class StructureLocationRoleService implements StructureLocationRoleServiceInterface
{

    /**
     *
     * @var RoleServiceInterface
     */
    protected $roleService;

    /**
     *
     * @var StructureLocationServiceInterface
     */
    protected $structureLocationService;

    /**
     *
     * @var StructureLocationRoleRepositoryInterface
     */
    protected $structureLocationRoleRepository;

    /**
     * @var Role[][]
     */
    protected $rolesPerStructureLocationCache;

    /**
     * StructureLocationRoleService constructor.
     *
     * @param RoleServiceInterface $roleService
     * @param StructureLocationServiceInterface $structureLocationService
     * @param StructureLocationRoleRepositoryInterface $structureLocationRoleRepository
     */
    public function __construct(
        RoleServiceInterface $roleService, StructureLocationServiceInterface $structureLocationService,
        StructureLocationRoleRepositoryInterface $structureLocationRoleRepository
    )
    {
        $this->roleService = $roleService;
        $this->structureLocationService = $structureLocationService;
        $this->structureLocationRoleRepository = $structureLocationRoleRepository;
    }

    /**
     * Adds a role to a given structure location
     *
     * @param StructureLocation $structureLocation
     * @param string $roleName
     *
     * @return StructureLocationRole
     *
     * @throws \Exception
     */
    public function addRoleToStructureLocation(StructureLocation $structureLocation, $roleName)
    {
        $role = $this->roleService->getOrCreateRoleByName($roleName);

        $structureLocationRole = new StructureLocationRole();

        $structureLocationRole->setRoleId($role->getId());
        $structureLocationRole->setStructureLocationId($structureLocation->getId());

        if (!$this->structureLocationRoleRepository->create($structureLocationRole))
        {
            throw new \Exception(
                'The structure location role for context ' . $structureLocation->getContext() . ', action ' .
                $structureLocation->getAction() . ' and role ' . $roleName . ' could not be created'
            );
        }

        return $structureLocationRole;
    }

    /**
     * Removes a role from a given structure location
     *
     * @param StructureLocation $structureLocation
     * @param string $roleName
     *
     * @throws \Exception
     */
    public function removeRoleFromStructureLocation(StructureLocation $structureLocation, $roleName)
    {
        try
        {
            $role = $this->roleService->getRoleByName($roleName);
        }
        catch (\Exception $ex)
        {
            return;
        }

        $structureLocationRole =
            $this->structureLocationRoleRepository->findStructureLocationRoleByStructureLocationAndRole(
                $structureLocation->getId(),
                $role->getId()
            );

        if (!$structureLocationRole instanceof StructureLocationRole)
        {
            return;
        }

        if (!$this->structureLocationRoleRepository->delete($structureLocationRole))
        {
            throw new \Exception(
                'The structure location role for context ' . $structureLocation->getContext() . ', action ' .
                $structureLocation->getAction() . ' and role ' . $roleName . ' could not be removed'
            );
        }
    }

    /**
     * Returns a list of roles for a given structure location identified by given context and comnponent
     *
     * @param string $context
     * @param string|null $action
     *
     * @return \Chamilo\Core\User\Roles\Storage\DataClass\Role[]
     */
    public function getRolesForLocationByContextAndAction(string $context, string $action = null)
    {
        $rolesPerStructureLocation = $this->loadStructureLocationsAndRoles();

        return $rolesPerStructureLocation[$context . '-' . $action];
    }

    /**
     * @return array|\Chamilo\Core\User\Roles\Storage\DataClass\Role[][]
     */
    protected function loadStructureLocationsAndRoles()
    {
        if (!isset($this->rolesPerStructureLocationCache))
        {
            $rolesPerStructureLocation = [];

            $structureLocationsAndRolesArray =
                $this->structureLocationRoleRepository->retrieveStructureLocationsAndRoles();

            foreach ($structureLocationsAndRolesArray as $structureLocationAndRoleArray)
            {
                $rolesPerStructureLocation[
                    $structureLocationAndRoleArray['context'] . '-' . $structureLocationAndRoleArray['action']
                ][] = new Role($structureLocationAndRoleArray);
            }

            $this->rolesPerStructureLocationCache = $rolesPerStructureLocation;
        }

        return $this->rolesPerStructureLocationCache;
    }
}
