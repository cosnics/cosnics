<?php
namespace Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;
use Chamilo\Libraries\Storage\DataManager\Interfaces\DataManagerRepositoryInterface;

/**
 * Repository to manage the data of roles
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface StructureLocationRepositoryInterface extends DataManagerRepositoryInterface
{

    /**
     * Returns a structure location by a given context and action
     * 
     * @param string $context
     * @param string $action
     *
     * @return StructureLocation
     */
    public function findStructureLocationByContextAndAction($context, $action = null);

    /**
     * Truncates the structure locations and roles for the structure locations
     * 
     * @return bool
     */
    public function truncateStructureLocationsAndRoles();
}