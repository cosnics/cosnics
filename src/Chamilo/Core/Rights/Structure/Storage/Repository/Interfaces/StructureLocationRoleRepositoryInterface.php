<?php
namespace Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;

/**
 * Repository to manage the data of roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface StructureLocationRoleRepositoryInterface
{
    /**
     * Returns a structure location role object by a given structure location and role
     *
     * @param int $structureLocationId
     * @param int $roleId
     *
     * @return StructureLocation
     */
    public function findStructureLocationRoleByStructureLocationAndRole($structureLocationId, $roleId);

    /**
     * Returns a list of roles by a given structure location
     *
     * @param int $structureLocationId
     *
     * @return Role[]
     */
    public function findRolesForStructureLocation($structureLocationId);
}