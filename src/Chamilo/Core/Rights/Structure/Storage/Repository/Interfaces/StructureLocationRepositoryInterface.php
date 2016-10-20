<?php
namespace Chamilo\Core\Rights\Structure\Storage\Repository\Interfaces;

use Chamilo\Core\Rights\Structure\Storage\DataClass\StructureLocation;

/**
 * Repository to manage the data of roles
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface StructureLocationRepositoryInterface
{
    /**
     * Returns a structure location by a given context and component
     *
     * @param string $context
     * @param string $component
     *
     * @return StructureLocation
     */
    public function findStructureLocationByContextAndComponent($context, $component = null);
}