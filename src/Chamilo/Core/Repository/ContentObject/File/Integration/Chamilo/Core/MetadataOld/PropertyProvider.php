<?php
namespace Chamilo\Core\Repository\ContentObject\File\MetadataPropertyLinker;

/**
 * This class provides and renders the properties to link to a metadata element
 *
 * @package repository\content_object\file\integration\repository\content_object_metadata_element_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PropertyProvider implements
    \Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\PropertyProvider\PropertyProviderInterface
{
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_FILE_EXTENSION = 'file_extension';
    const PROPERTY_FILESIZE = 'filesize';

    /**
     * Returns the properties that can be linked to the metadata elements
     *
     * @return string[]
     */
    public function get_properties()
    {
        return array(self :: PROPERTY_FILENAME, self :: PROPERTY_FILE_EXTENSION, self :: PROPERTY_FILESIZE);
    }

    /**
     * Renders a property for a given content object
     *
     * @param string $property
     * @param \core\repository\ContentObject $content_object
     *
     * @return string
     */
    public function render_property($property,\Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object)
    {
        switch ($property)
        {
            case self :: PROPERTY_FILE_EXTENSION :
                return $content_object->get_extension();
        }

        return $content_object->get_additional_property($property);
    }
}