<?php
namespace Chamilo\Core\Metadata\Element\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes the association between an element and another element or an attribute based on an attributes
 * value The parent property describes an element.
 * The child can either be an element or an attribute. (depending on the
 * type)
 * 
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ElementRelAttribute extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ELEMENT_ID = 'element_id';
    const PROPERTY_ATTRIBUTE_ID = 'attribute_id';

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
        $extended_property_names[] = self :: PROPERTY_ELEMENT_ID;
        $extended_property_names[] = self :: PROPERTY_ATTRIBUTE_ID;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the element_id
     * 
     * @return int
     */
    public function get_element_id()
    {
        return $this->get_default_property(self :: PROPERTY_ELEMENT_ID);
    }

    /**
     * Sets the element_id
     * 
     * @param int $element_id
     */
    public function set_element_id($element_id)
    {
        $this->set_default_property(self :: PROPERTY_ELEMENT_ID, $element_id);
    }

    /**
     * Returns the attribute_id
     * 
     * @return int
     */
    public function get_attribute_id()
    {
        return $this->get_default_property(self :: PROPERTY_ATTRIBUTE_ID);
    }

    /**
     * Sets the attribute_id
     * 
     * @param int $attribute_id
     */
    public function set_attribute_id($attribute_id)
    {
        $this->set_default_property(self :: PROPERTY_ATTRIBUTE_ID, $attribute_id);
    }
}
