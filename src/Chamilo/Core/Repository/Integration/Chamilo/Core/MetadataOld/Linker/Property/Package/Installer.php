<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Package;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\IntegrationInstaller;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\PropertyProvider\PropertyProvider;

/**
 * Installer class for this package
 * 
 * @package repository\integration\core\metadata\linker\property
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
        
        $properties[PropertyProvider :: PROPERTY_TITLE] = 'dc:title';
        $properties[PropertyProvider :: PROPERTY_DESCRIPTION] = 'dc:description';
        $properties[PropertyProvider :: PROPERTY_TAGS] = 'dc:subject';
        $properties[PropertyProvider :: PROPERTY_CREATION_DATE] = 'dc:date';
        $properties[PropertyProvider :: PROPERTY_OWNER_FULLNAME] = 'dc:creator';
        $properties[PropertyProvider :: PROPERTY_IDENTIFIER] = 'dc:identifier';
        $properties[PropertyProvider :: PROPERTY_TYPE] = 'dc:type';
        
        return $properties;
    }
}
