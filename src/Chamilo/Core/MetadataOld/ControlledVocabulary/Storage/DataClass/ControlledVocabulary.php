<?php
namespace Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\AttributeControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementControlledVocabulary;
use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Core\MetadataOld\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a controlled vocabulary
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ControlledVocabulary extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_VALUE = 'value';

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
        $extended_property_names[] = self :: PROPERTY_VALUE;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     */
    protected function get_dependencies()
    {
        return parent :: get_dependencies(
            array(
                ElementControlledVocabulary :: class_name() => $this->get_element_controlled_vocabulary_dependency_condition(), 
                AttributeControlledVocabulary :: class_name() => $this->get_attribute_controlled_vocabulary_dependency_condition(), 
                DefaultElementValue :: class_name() => $this->get_default_element_value_dependency_condition(), 
                DefaultAttributeValue :: class_name() => $this->get_default_attribute_value_dependency_condition()));
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the value
     * 
     * @return int - the value.
     */
    public function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value
     * 
     * @param int $value
     */
    public function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the condition for the element controlled vocabulary dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_element_controlled_vocabulary_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ElementControlledVocabulary :: class_name(), 
                ElementControlledVocabulary :: PROPERTY_CONTROLLED_VOCABULARY_ID), 
            new StaticConditionVariable($this->get_id()));
    }

    /**
     * Returns the condition for the attribute controlled vocabulary dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_attribute_controlled_vocabulary_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                AttributeControlledVocabulary :: class_name(), 
                AttributeControlledVocabulary :: PROPERTY_CONTROLLED_VOCABULARY_ID), 
            new StaticConditionVariable($this->get_id()));
    }

    /**
     * Returns the condition for the default element value dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_default_element_value_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(), 
                DefaultElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID), 
            new StaticConditionVariable($this->get_id()));
    }

    /**
     * Returns the condition for the default attribute value dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_default_attribute_value_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                DefaultAttributeValue :: class_name(), 
                DefaultAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID), 
            new StaticConditionVariable($this->get_id()));
    }
}