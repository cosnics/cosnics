<?php
namespace Chamilo\Core\MetadataOld\Value\Storage\DataClass;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This abstract class describes the connection between an object (like a user, a group, a content object) and a
 * metadata element
 * 
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
abstract class ElementValue extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ELEMENT_ID = 'element_id';
    const PROPERTY_ELEMENT_VOCABULARY_ID = 'element_vocabulary_id';
    const PROPERTY_VALUE = 'value';

    /**
     * ***************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    
    /**
     * A controlled vocabulary
     * 
     * @var \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary
     */
    private $controlled_vocabulary;

    /**
     * The attribute values connected to this element value
     * 
     * @var AttributeValue[]
     */
    private $attribute_values;

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
        $extended_property_names[] = self :: PROPERTY_ELEMENT_VOCABULARY_ID;
        $extended_property_names[] = self :: PROPERTY_VALUE;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the element_id of this MetadataElementValue.
     * 
     * @return int
     */
    public function get_element_id()
    {
        return $this->get_default_property(self :: PROPERTY_ELEMENT_ID);
    }

    /**
     * Sets the element_id of this MetadataElementValue.
     * 
     * @param int
     */
    public function set_element_id($element_id)
    {
        $this->set_default_property(self :: PROPERTY_ELEMENT_ID, $element_id);
    }

    /**
     * Returns the element_vocabulary_id of this MetadataElementValue.
     * 
     * @return int
     */
    public function get_element_vocabulary_id()
    {
        return $this->get_default_property(self :: PROPERTY_ELEMENT_VOCABULARY_ID);
    }

    /**
     * Sets the element_vocabulary_id of this MetadataElementValue.
     * 
     * @param int
     */
    public function set_element_vocabulary_id($element_vocabulary_id)
    {
        $this->set_default_property(self :: PROPERTY_ELEMENT_VOCABULARY_ID, $element_vocabulary_id);
    }

    /**
     * Returns the value of this MetadataElementValue.
     * 
     * @return string
     */
    public function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of this MetadataElementValue.
     * 
     * @param string
     */
    public function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Sets the controlled vocabulary
     * 
     * @param \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary $controlled_vocabulary
     */
    public function set_controlled_vocabulary(ControlledVocabulary $controlled_vocabulary)
    {
        $this->controlled_vocabulary = $controlled_vocabulary;
    }

    /**
     * Returns the controlled vocabulary
     * 
     * @return \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary
     */
    public function get_controlled_vocabulary()
    {
        return $this->controlled_vocabulary;
    }

    /**
     * Sets the attribute values
     * 
     * @param \Chamilo\Core\MetadataOld\value\storage\data_class\AttributeValue[] $attribute_values
     */
    public function set_attribute_values($attribute_values)
    {
        $this->attribute_values = $attribute_values;
    }

    /**
     * Returns the attribute values
     * 
     * @return \Chamilo\Core\MetadataOld\value\storage\data_class\AttributeValue[]
     */
    public function get_attribute_values()
    {
        return $this->attribute_values;
    }

    /**
     * Adds an attribute value to the array of attribute values
     * 
     * @param AttributeValue $attribute_value
     */
    public function add_attribute_value(AttributeValue $attribute_value)
    {
        $this->attribute_values[] = $attribute_value;
    }
}