<?php
namespace Chamilo\Core\Metadata\Element\Storage\DataClass;

use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabularyRelation;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
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
class ElementControlledVocabulary extends ControlledVocabularyRelation
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_ELEMENT_ID = 'element_id';

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
                \Chamilo\Core\Metadata\Manager :: context(), 
                array(
                    ImplementationNotifierDataClassListener :: GET_DEPENDENCIES => 'get_element_controlled_vocabulary_dependencies')));
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
        $extended_property_names[] = self :: PROPERTY_ELEMENT_ID;
        
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
            array(DefaultElementValue :: class_name() => $this->get_default_element_value_dependency_condition()));
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the element_id
     * 
     * @return int - the element_id.
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
     * **************************************************************************************************************
     * Protected helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the condition for the element association dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_default_element_value_dependency_condition()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(), 
                DefaultElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($this->get_element_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(), 
                DefaultElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID), 
            new StaticConditionVariable($this->get_controlled_vocabulary_id()));
        
        return new AndCondition($conditions);
    }
}