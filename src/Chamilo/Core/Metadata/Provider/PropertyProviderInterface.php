<?php
namespace Chamilo\Core\Metadata\Provider;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This interface provides the class structure to provide and renders the properties to link to a metadata element
 * 
 * @package Chamilo\Core\Metadata\Provider
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface PropertyProviderInterface
{

    /**
     * Get the fully qualified class name of the object instance
     * 
     * @return string
     */
    public static function class_name($fully_qualified = true, $camel_case = true);

    /**
     * Get the namespace of the object instance
     * 
     * @return string
     */
    public static function context();

    /**
     * Returns the fully qualified class name of the entity the property provider provides properties for
     * 
     * @return string
     */
    public function getEntityType();

    /**
     * Returns the properties that can be linked to the metadata elements
     * 
     * @return string[]
     */
    public function getAvailableProperties();

    /**
     * Renders a property for a given entity
     * 
     * @param string $property
     * @param DataClass $dataClass
     *
     * @return string
     */
    public function renderProperty($property, DataClass $dataClass);
}