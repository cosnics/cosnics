<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes an attribute of the metadata
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectRelMetadataElement extends DataClass
{
    /**
     * ***************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_METADATA_ELEMENT_ID = 'metadata_element_id';
    const PROPERTY_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PROPERTY_REQUIRED = 'required';

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
        $extended_property_names[] = self :: PROPERTY_REQUIRED;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * Updates the object
     * Need to execute this because when a property is null, the property is not correctly escaped for the database
     * 
     * @throws \Exception
     *
     * @return boolean
     */
    public function update()
    {
        if (is_null($this->get_content_object_type()))
        {
            $properties = $this->get_default_properties();
            unset($properties[self :: PROPERTY_CONTENT_OBJECT_TYPE]);
            $this->set_default_properties($properties);
        }
        
        return parent :: update();
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
     * Returns if this field is required
     * 
     * @return string
     */
    public function is_required()
    {
        return $this->get_default_property(self :: PROPERTY_REQUIRED);
    }

    /**
     * Sets the required
     * 
     * @param string $required
     */
    public function set_required($required)
    {
        $this->set_default_property(self :: PROPERTY_REQUIRED, $required);
    }
}