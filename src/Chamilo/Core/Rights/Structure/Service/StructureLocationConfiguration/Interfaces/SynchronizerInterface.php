<?php
namespace Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Interfaces;

/**
 * Synchronizes configuration files where default structure locations and roles are defined with the database
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface SynchronizerInterface
{
    /**
     * Synchronizes configuration files where default structure locations and roles are defined with the database
     */
    public function synchronize();
}