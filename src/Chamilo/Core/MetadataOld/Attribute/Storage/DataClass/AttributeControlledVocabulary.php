<?php
namespace Chamilo\Core\MetadataOld\Attribute\Storage\DataClass;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabularyRelation;
use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Libraries\Storage\DataClass\Listeners\ImplementationNotifierDataClassListener;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a controlled vocabulary for an element
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AttributeControlledVocabulary extends ControlledVocabularyRelation
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ATTRIBUTE_ID = 'attribute_id';

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param array $default_properties
     * @param array $optional_properties
     */
    public function __construct($default_properties = array(), $optional_properties = array())
    {
        parent :: __construct($default_properties = $optional_properties);
        
        $this->add_listener(
            new ImplementationNotifierDataClassListener(
                $this, 
                \Chamilo\Core\MetadataOld\Manager :: context(), 
                array(
                    ImplementationNotifierDataClassListener :: GET_DEPENDENCIES => 'get_attribute_controlled_vocabulary_dependencies')));
    }

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
            array(DefaultAttributeValue :: class_name() => $this->get_default_attribute_value_dependency_condition()));
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the attribute_id
     * 
     * @return int - the attribute_id.
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
     * **************************************************************************************************************
     * Protected helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the condition for the attribute association dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_default_attribute_value_dependency_condition()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultAttributeValue :: class_name(), 
                DefaultAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($this->get_attribute_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultAttributeValue :: class_name(), 
                DefaultAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID), 
            new StaticConditionVariable($this->get_controlled_vocabulary_id()));
        
        return new AndCondition($conditions);
    }
}