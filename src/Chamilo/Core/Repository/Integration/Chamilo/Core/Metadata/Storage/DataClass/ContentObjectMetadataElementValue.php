<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Value\Storage\DataClass\ElementValue;

/**
 * Class to store the element values for the given content object
 * 
 * @package repository\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectMetadataElementValue extends ElementValue
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

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
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * ***************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the content_object_id
     * 
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id
     * 
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }
}