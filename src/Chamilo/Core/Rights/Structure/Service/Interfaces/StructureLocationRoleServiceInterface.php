<?php
namespace Chamilo\Core\Rights\Structure\Service\Interfaces;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocationRole;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;

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
     * Returns a list of roles for a given structure location identified by given context and comnponent
     *
     * @param string $context
     * @param string|null $action
     *
     * @return \Chamilo\Core\User\Roles\Storage\DataClass\Role[]
     */
    public function getRolesForLocationByContextAndAction(string $context, string $action = null);
}
