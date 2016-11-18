<?php
namespace Chamilo\Core\Rights\Structure\Service;

use Chamilo\Core\Rights\Structure\Service\Interfaces\StructureLocationRoleServiceInterface;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocationRole;
use Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces\StructureLocationRoleRepositoryInterface;
use Chamilo\Core\User\Roles\Service\Interfaces\RoleServiceInterface;

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
     * @var StructureLocationService
     */
    protected $structureLocationService;

    /**
     *
     * @var StructureLocationRoleRepositoryInterface
     */
    protected $structureLocationRoleRepository;

    /**
     * StructureLocationRoleService constructor.
     * 
     * @param RoleServiceInterface $roleService
     * @param StructureLocationService $structureLocationService
     * @param StructureLocationRoleRepositoryInterface $structureLocationRoleRepository
     */
    public function __construct(RoleServiceInterface $roleService, StructureLocationService $structureLocationService, 
        StructureLocationRoleRepositoryInterface $structureLocationRoleRepository)
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
        
        if (! $structureLocationRole->create())
        {
            throw new \Exception(
                'The structure location role for context ' . $structureLocation->getContext() . ', action ' .
                     $structureLocation->getAction() . ' and role ' . $roleName . ' could not be created');
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
        
        $structureLocationRole = $this->structureLocationRoleRepository->findStructureLocationRoleByStructureLocationAndRole(
            $structureLocation->getId(), 
            $role->getId());
        
        if (! $structureLocationRole)
        {
            return;
        }
        
        if (! $structureLocationRole->delete())
        {
            throw new \Exception(
                'The structure location role for context ' . $structureLocation->getContext() . ', action ' .
                     $structureLocation->getAction() . ' and role ' . $roleName . ' could not be removed');
        }
    }

    /**
     * Returns a list of roles for a given structure location
     * 
     * @param StructureLocation $structureLocation
     *
     * @return Role[]
     */
    public function getRolesForLocation(StructureLocation $structureLocation)
    {
        return $this->structureLocationRoleRepository->findRolesForStructureLocation($structureLocation->getId());
    }

    /**
     * Returns a list of roles for a given structure location identified by given context and comnponent
     * 
     * @param string $context
     * @param string $action
     *
     * @return \Chamilo\Core\User\Roles\Storage\DataClass\Role[]
     */
    public function getRolesForLocationByContextAndAction($context, $action = null)
    {
        try
        {
            $structureLocation = $this->structureLocationService->getStructureLocationByContextAndAction(
                $context, 
                $action);
            
            return $this->getRolesForLocation($structureLocation);
        }
        catch (\Exception $ex)
        {
            return array();
        }
    }
}