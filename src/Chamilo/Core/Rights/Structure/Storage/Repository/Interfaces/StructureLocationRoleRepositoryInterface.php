<?php
namespace Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;

/**
 * Repository to manage the data of roles
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface StructureLocationRoleRepositoryInterface extends DataManagerRepositoryInterface
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
     * @return array[]
     */
    public function retrieveStructureLocationsAndRoles();

}
