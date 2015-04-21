<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Metadata\Storage\DataClass;

use Chamilo\Core\Metadata\Value\Storage\DataClass\AttributeValue;

/**
 * Class to store the attribute values for the given user
 * 
 * @package user\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataAttributeValue extends AttributeValue
{
    const PROPERTY_USER_ID = 'user_id';

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
        $extended_property_names[] = self :: PROPERTY_USER_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * ***************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the user_id
     * 
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id
     * 
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
}