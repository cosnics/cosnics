<?php
namespace Chamilo\Core\Repository\ContentObject\File\MetadataPropertyLinker\Package;

use Chamilo\Core\Repository\ContentObject\File\MetadataPropertyLinker\PropertyProvider;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\IntegrationInstaller;

/**
 * Installs this package
 *
 * @package repository\content_object\file\integration\core\repository\content_object_metadata_element_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Installer extends IntegrationInstaller
{

    /**
     * Returns an array of the properties that need to be linked to elements by default The array is build with the
     * property name as key, and the schema_name:element_name as value
     *
     * @example $elements[PropertyProvider::PROPERTY_TITLE] = 'dc:title'
     * @return mixed
     */
    function get_properties()
    {
        $properties = array();

        $properties[PropertyProvider :: PROPERTY_FILE_EXTENSION] = 'dc:format';

        return $properties;
    }
}
