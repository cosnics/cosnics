<?php
namespace Chamilo\Core\Rights\Structure\Service\StructureLocationConfiguration\Interfaces;

/**
 * Loads structure location configuration from the packages
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface LoaderInterface
{

    /**
     * Loads the structure location configuration from the given packages
     * 
     * @param array $packageNamespaces
     *
     * @return string[]
     */
    public function loadConfiguration($packageNamespaces = array());
}