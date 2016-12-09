<?php
namespace Chamilo\Core\Repository\Publication\Publisher\Interfaces;

/**
 * Interface that describes the necessary functions for a publication handler
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface PublicationHandlerInterface
{

    /**
     * Publishes the actual selected and configured content objects
     * 
     * @param ContentObject[] $selectedContentObjects
     */
    public function publish($selectedContentObjects = array());
}