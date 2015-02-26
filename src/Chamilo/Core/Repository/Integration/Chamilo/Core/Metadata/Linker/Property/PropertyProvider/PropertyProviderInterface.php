<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\PropertyProvider;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * This interface provides the class structure to provide and renders the properties to link to a metadata element
 * 
 * @package repository\content_object_property_metadata_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface PropertyProviderInterface
{

    /**
     * Returns the properties that can be linked to the metadata elements
     * 
     * @return string[]
     */
    public function get_properties();

    /**
     * Renders a property for a given content object
     * 
     * @param string $property
     * @param ContentObject $content_object
     *
     * @return string
     */
    public function render_property($property, ContentObject $content_object);
}