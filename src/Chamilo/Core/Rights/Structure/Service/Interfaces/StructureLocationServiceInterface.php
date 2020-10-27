<?php
namespace Chamilo\Core\Rights\Structure\Service\Interfaces;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;

/**
 * Manages structure locations
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface StructureLocationServiceInterface
{

    /**
     * Creates a structure location based on a given context and action
     * 
     * @param string $context
     * @param string $action
     *
     * @return StructureLocation
     *
     * @throws \Exception
     */
    public function createStructureLocation($context, $action = null);

    /**
     * Deletes a given structure location
     * 
     * @param StructureLocation $structureLocation
     *
     * @throws \Exception
     */
    public function deleteStructureLocation(StructureLocation $structureLocation);

    /**
     * Truncates the structure locations with their roles
     * 
     * @throws \Exception
     */
    public function truncateStructureLocations();
}
