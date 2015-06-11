<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes the relation between a content object property and a metadata element
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPropertyRelMetadataElement extends DataClass
{
    /**
     * ***************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_METADATA_ELEMENT_ID = 'metadata_element_id';
    const PROPERTY_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PROPERTY_PROPERTY_NAME = 'property_name';

    /**
     * ***************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Get the default properties
     * 
     * @param array $extended_property_names
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_METADATA_ELEMENT_ID;
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_TYPE;
        $extended_property_names[] = self :: PROPERTY_PROPERTY_NAME;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * ***************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the metadata_element_id
     * 
     * @return int
     */
    public function get_metadata_element_id()
    {
        return $this->get_default_property(self :: PROPERTY_METADATA_ELEMENT_ID);
    }

    /**
     * Sets the metadata_element_id
     * 
     * @param int $metadata_element_id
     */
    public function set_metadata_element_id($metadata_element_id)
    {
        $this->set_default_property(self :: PROPERTY_METADATA_ELEMENT_ID, $metadata_element_id);
    }

    /**
     * Returns the content_object_type
     * 
     * @return string
     */
    public function get_content_object_type()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE);
    }

    /**
     * Sets the content_object_type
     * 
     * @param string $content_object_type
     */
    public function set_content_object_type($content_object_type)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE, $content_object_type);
    }

    /**
     * Returns the property_name
     * 
     * @return string
     */
    public function get_property_name()
    {
        return $this->get_default_property(self :: PROPERTY_PROPERTY_NAME);
    }

    /**
     * Sets the property_name
     * 
     * @param string $property_name
     */
    public function set_property_name($property_name)
    {
        $this->set_default_property(self :: PROPERTY_PROPERTY_NAME, $property_name);
    }
}