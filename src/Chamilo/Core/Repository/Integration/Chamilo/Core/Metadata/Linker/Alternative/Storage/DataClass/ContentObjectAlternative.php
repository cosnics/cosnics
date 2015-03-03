<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataClass;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes a link between two content objects based on a metadata element (and it's values)
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectAlternative extends DataClass
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    
    /**
     * This field is used to determine a unique link between two content objects.
     * The two linked objects can not be in
     * the same row because a distinction between the two content objects should not be made. Ex: object a is linked to
     * object b, so object b is also linked to object a. When the two content object id's would be in the same row, it
     * would be very hard to join with the correct content object id, depending if you show the alternatives for object
     * a, or object b.
     */
    const PROPERTY_LINK_NUMBER = 'link_number';
    const PROPERTY_METADATA_ELEMENT_ID = 'metadata_element_id';
    const PROPERTY_DATE = 'date';

    /**
     * Get the default properties
     * 
     * @param array $extended_property_names
     *
     * @return array
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_CONTENT_OBJECT_ID, 
                self :: PROPERTY_LINK_NUMBER, 
                self :: PROPERTY_METADATA_ELEMENT_ID, 
                self :: PROPERTY_DATE));
    }

    /**
     * Deletes the object
     * 
     * @return boolean
     */
    public function delete()
    {
        if (! parent :: delete())
        {
            return false;
        }
        
        $linked_alternative_content_objects_count = DataManager :: count_content_object_alternatives_by_link_number(
            $this->get_link_number());
        
        if ($linked_alternative_content_objects_count == 1)
        {
            return DataManager :: delete_content_object_alternatives_by_link_number($this->get_link_number());
        }
        
        return true;
    }

    /**
     * Returns the content_object_id of this ContentObjectAlternative.
     * 
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id of this ContentObjectAlternative.
     * 
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * Returns the link_number of this ContentObjectAlternative.
     * 
     * @return int
     */
    public function get_link_number()
    {
        return $this->get_default_property(self :: PROPERTY_LINK_NUMBER);
    }

    /**
     * Sets the link_number of this ContentObjectAlternative.
     * 
     * @param int $link_number
     */
    public function set_link_number($link_number)
    {
        $this->set_default_property(self :: PROPERTY_LINK_NUMBER, $link_number);
    }

    /**
     * Returns the metadata_element_id of this ContentObjectAlternative.
     * 
     * @return int
     */
    public function get_metadata_element_id()
    {
        return $this->get_default_property(self :: PROPERTY_METADATA_ELEMENT_ID);
    }

    /**
     * Sets the metadata_element_id of this ContentObjectAlternative.
     * 
     * @param int $metadata_element_id
     */
    public function set_metadata_element_id($metadata_element_id)
    {
        $this->set_default_property(self :: PROPERTY_METADATA_ELEMENT_ID, $metadata_element_id);
    }

    /**
     * Returns the date of this ContentObjectAlternative.
     * 
     * @return int
     */
    public function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    /**
     * Sets the date of this ContentObjectAlternative.
     * 
     * @param int $date
     */
    public function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }
}