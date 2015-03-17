<?php
namespace Chamilo\Core\Metadata\Element\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes the nesting of an element or an attribute into another element
 * 
 * @author Jens Vanderheyden - VUB Brussel
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementNesting extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_PARENT_ELEMENT_ID = 'parent_element_id';
    const PROPERTY_CHILD_ELEMENT_ID = 'child_element_id';

    /**
     * **************************************************************************************************************
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
        $extended_property_names[] = self :: PROPERTY_PARENT_ELEMENT_ID;
        $extended_property_names[] = self :: PROPERTY_CHILD_ELEMENT_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the parent_element_id
     * 
     * @return int
     */
    public function get_parent_element_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ELEMENT_ID);
    }

    /**
     * Sets the parent_element_id
     * 
     * @param int $parent_element_id
     */
    public function set_parent_element_id($parent_element_id)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ELEMENT_ID, $parent_element_id);
    }

    /**
     * Returns the child_element_id
     * 
     * @return int
     */
    public function get_child_element_id()
    {
        return $this->get_default_property(self :: PROPERTY_CHILD_ELEMENT_ID);
    }

    /**
     * Sets the child_element_id
     * 
     * @param int $child_element_id
     */
    public function set_child_element_id($child_element_id)
    {
        $this->set_default_property(self :: PROPERTY_CHILD_ELEMENT_ID, $child_element_id);
    }
}