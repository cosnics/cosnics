<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Libraries\Platform\Translation;

/**
 * Abstract class to define a base for the installer of the integration packages for this package
 * 
 * @package repository\content_object_property_metadata_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class IntegrationInstaller extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Installs additional features for this package
     */
    public function extra()
    {
        return $this->install_content_object_property_rel_metadata_elements();
    }

    /**
     * Installs the default ContentObjectPropertyRelMetadataElements
     */
    protected function install_content_object_property_rel_metadata_elements()
    {
        $properties = $this->get_properties();
        
        foreach ($properties as $property => $element)
        {
            if (! $this->install_content_object_property_rel_metadata_element($this->context(), $property, $element))
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Installs a single ContentObjectPropertyRelMetadataElement
     * 
     * @param string $content_object_type
     * @param string $property_name
     * @param string $element_name
     *
     * @return bool
     */
    protected function install_content_object_property_rel_metadata_element($content_object_type, $property_name, 
        $element_name)
    {
        try
        {
            $element_name_parts = explode(':', $element_name);
            $element = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_element_by_schema_namespace_and_element_name(
                $element_name_parts[0], 
                $element_name_parts[1]);
            
            $content_object_rel_metadata_element = new ContentObjectPropertyRelMetadataElement();
            
            $content_object_rel_metadata_element->set_content_object_type($content_object_type);
            $content_object_rel_metadata_element->set_property_name($property_name);
            $content_object_rel_metadata_element->set_metadata_element_id($element->get_id());
            
            if ($content_object_rel_metadata_element->create())
            {
                $this->add_message(
                    self :: TYPE_NORMAL, 
                    Translation :: get(
                        'ObjectCreated', 
                        array('OBJECT' => Translation :: get('ContentObjectPropertyRelMetadataElement'))));
            }
            else
            {
                return $this->failed(
                    Translation :: get(
                        'ObjectNotCreated', 
                        array('OBJECT' => Translation :: get('ContentObjectPropertyRelMetadataElement'))));
            }
        }
        catch (\Exception $e)
        {
            return $this->failed($e->getMessage());
        }
        
        return true;
    }

    /**
     * Returns an array of the properties that need to be linked to elements by default The array is build with the
     * property name as key, and the schema_name:element_name as value
     * 
     * @example $elements[PropertyProvider::PROPERTY_TITLE] = 'dc:title'
     * @return mixed
     */
    abstract function get_properties();
}
