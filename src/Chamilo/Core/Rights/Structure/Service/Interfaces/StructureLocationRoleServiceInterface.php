<?php
namespace Chamilo\Core\Rights\Structure\Service\Interfaces;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocationRole;

/**
 * Manages structure location roles
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface StructureLocationRoleServiceInterface
{

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
    public function addRoleToStructureLocation(StructureLocation $structureLocation, $roleName);

    /**
     * Removes a role from a given structure location
     * 
     * @param StructureLocation $structureLocation
     * @param string $roleName
     *
     * @throws \Exception
     */
    public function removeRoleFromStructureLocation(StructureLocation $structureLocation, $roleName);

    /**
     * Returns a list of roles for a given structure location
     * 
     * @param StructureLocation $structureLocation
     *
     * @return Role[]
     */
    public function getRolesForLocation(StructureLocation $structureLocation);

    /**
     * Returns a list of roles for a given structure location identified by given context and comnponent
     * 
     * @param string $context
     * @param string $action
     *
     * @return \Chamilo\Core\User\Roles\Storage\DataClass\Role[]
     */
    public function getRolesForLocationByContextAndAction($context, $action = null);
}