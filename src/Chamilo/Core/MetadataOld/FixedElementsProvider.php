<?php
namespace Chamilo\Core\MetadataOld;

/**
 * This interface determines a base layout for the classes that provide fixed metadata elements
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface FixedElementsProvider
{

    /**
     * Returns an array with the element id and the value for the fixed elements
     * 
     * @return string[int]
     */
    public function get_fixed_elements();
}