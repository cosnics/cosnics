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
     * Creates a structure location based on a given context and component
     *
     * @param string $context
     * @param string $component
     *
     * @return StructureLocation
     *
     * @throws \Exception
     */
    public function createStructureLocation($context, $component = null);

    /**
     * Deletes a given structure location
     *
     * @param StructureLocation $structureLocation
     *
     * @throws \Exception
     */
    public function deleteRole(StructureLocation $structureLocation);

    /**
     * Returns the structure location by a given context and component
     *
     * @param string $context
     * @param string $component
     *
     * @return StructureLocation
     *
     * @throws \Exception
     */
    public function getStructureLocationByContextAndComponent($context, $component = null);
}