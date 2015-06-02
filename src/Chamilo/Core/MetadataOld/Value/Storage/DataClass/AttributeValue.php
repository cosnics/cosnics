<?php
namespace Chamilo\Core\MetadataOld\Value\Storage\DataClass;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class describes a value for a metadata attribute of a content object
 * 
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
abstract class AttributeValue extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ATTRIBUTE_ID = 'attribute_id';
    const PROPERTY_ELEMENT_VALUE_ID = 'element_value_id';
    const PROPERTY_ATTRIBUTE_VOCABULARY_ID = 'attribute_vocabulary_id';
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
        $extended_property_names[] = self :: PROPERTY_ATTRIBUTE_ID;
        $extended_property_names[] = self :: PROPERTY_ELEMENT_VALUE_ID;
        $extended_property_names[] = self :: PROPERTY_ATTRIBUTE_VOCABULARY_ID;
        $extended_property_names[] = self :: PROPERTY_VALUE;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
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

    /**
     * Returns the element_value_id
     * 
     * @return int
     */
    public function get_element_value_id()
    {
        return $this->get_default_property(self :: PROPERTY_ELEMENT_VALUE_ID);
    }

    /**
     * Sets the element_value_id
     * 
     * @param int $element_value_id
     */
    public function set_element_value_id($element_value_id)
    {
        $this->set_default_property(self :: PROPERTY_ELEMENT_VALUE_ID, $element_value_id);
    }

    /**
     * Sets the attribute_vocabulary_id
     * 
     * @return int
     */
    public function get_attribute_vocabulary_id()
    {
        return $this->get_default_property(self :: PROPERTY_ATTRIBUTE_VOCABULARY_ID);
    }

    /**
     * Returns the attribute_vocabulary_id
     * 
     * @param int $attribute_vocabulary_id
     */
    public function set_attribute_vocabulary_id($attribute_vocabulary_id)
    {
        $this->set_default_property(self :: PROPERTY_ATTRIBUTE_VOCABULARY_ID, $attribute_vocabulary_id);
    }

    /**
     * Returns the value
     * 
     * @return string
     */
    public function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value
     * 
     * @param string $value
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
}
