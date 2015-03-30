<?php
namespace Chamilo\Core\Metadata\Element\Storage\DataClass;

use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\DataClass\Listeners\ImplementationNotifierDataClassListener;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes an element in a metadata schema
 * 
 * @author Jens Vanderheyden - VUB Brussel
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Element extends DataClass implements DisplayOrderDataClassListenerSupport
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_SCHEMA_ID = 'schema_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DISPLAY_NAME = 'display_name';
    const PROPERTY_FIXED = 'fixed';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    private $namespace = false;

    /**
     * The nested elements for this element
     * 
     * @var Element[]
     */
    private $nested_elements;

    /**
     * The attributes for this element
     * 
     * @var \Chamilo\Core\Metadata\attribute\storage\data_class\Attribute[]
     */
    private $attributes;

    /**
     * The default values for this element
     * 
     * @var \Chamilo\Core\Metadata\value\element\storage\data_class\DefaultElementValue[]
     */
    private $default_values;

    /**
     * The controlled vocabulary for this element
     * 
     * @var \Chamilo\Core\Metadata\controlled_vocabulary\storage\data_class\ControlledVocabulary[]
     */
    private $controlled_vocabulary;

    /**
     * This property can be set from any given context to determine that this element should be required, this property
     * is mainly used in the SimpleElementForm where some elements can be made required, and others not, depending on
     * the given context
     * 
     * @var bool
     */
    private $required;

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
        parent :: __construct($default_properties, $optional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
        
        $this->add_listener(
            new ImplementationNotifierDataClassListener(
                $this, 
                \Chamilo\Core\Metadata\Manager :: context(), 
                array(ImplementationNotifierDataClassListener :: GET_DEPENDENCIES => 'get_element_dependencies')));
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
        $extended_property_names[] = self :: PROPERTY_SCHEMA_ID;
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_DISPLAY_NAME;
        $extended_property_names[] = self :: PROPERTY_FIXED;
        $extended_property_names[] = self :: PROPERTY_DISPLAY_ORDER;
        
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
                ElementNesting :: class_name() => $this->get_element_nesting_dependency_condition(), 
                ElementRelAttribute :: class_name() => $this->get_element_rel_attribute_dependency_condition(), 
                DefaultElementValue :: class_name() => $this->get_default_value_dependency_condition(), 
                ElementControlledVocabulary :: class_name() => $this->get_element_controlled_vocabulary_dependency_condition()));
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the schema_id
     * 
     * @return int
     */
    public function get_schema_id()
    {
        return $this->get_default_property(self :: PROPERTY_SCHEMA_ID);
    }

    /**
     * Sets the schema_id
     * 
     * @param int $schema_id
     */
    public function set_schema_id($schema_id)
    {
        $this->set_default_property(self :: PROPERTY_SCHEMA_ID, $schema_id);
    }

    /**
     * Returns the name
     * 
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name
     * 
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the display_name
     * 
     * @return string
     */
    public function get_display_name()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_NAME);
    }

    /**
     * Sets the display_name
     * 
     * @param string $display_name
     */
    public function set_display_name($display_name)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_NAME, $display_name);
    }

    /**
     * Returns whether or not this element is fixed
     * 
     * @return string
     */
    public function is_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_FIXED);
    }

    /**
     * Sets whether or not the element is fixed
     * 
     * @param string $fixed
     */
    public function set_fixed($fixed)
    {
        $this->set_default_property(self :: PROPERTY_FIXED, $fixed);
    }

    /**
     * Returns the display_order
     * 
     * @return int
     */
    public function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Sets the display_order
     * 
     * @param int display_order
     */
    public function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the prefix of the schema namespace
     * 
     * @return string
     */
    public function get_namespace()
    {
        if (! $this->namespace)
        {
            $schema = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_by_id(
                Schema :: class_name(), 
                $this->get_schema_id());
            
            if (! $schema)
            {
                return false;
            }
            
            $this->set_namespace($schema->get_namespace());
        }
        
        return $this->namespace;
    }

    /**
     * Sets the prefix of the schema namespace
     * 
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Sets the attributes
     * 
     * @param \Chamilo\Core\Metadata\attribute\storage\data_class\Attribute[] $attributes
     */
    public function set_attributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns the attributes
     * 
     * @return \Chamilo\Core\Metadata\attribute\storage\data_class\Attribute[]
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the nested elements
     * 
     * @param \Chamilo\Core\Metadata\element\storage\data_class\Element[] $nested_elements
     */
    public function set_nested_elements($nested_elements)
    {
        $this->nested_elements = $nested_elements;
    }

    /**
     * Returns the nested elements, lazy loading when the nested elements are not set
     * 
     * @return \Chamilo\Core\Metadata\element\storage\data_class\Element[]
     */
    public function get_nested_elements()
    {
        if (! isset($this->nested_elements))
        {
            $this->nested_elements = DataManager :: retrieve_nested_elements_for_element($this->get_id())->as_array();
        }
        
        return $this->nested_elements;
    }

    /**
     * Sets the controlled vocabulary
     * 
     * @param \Chamilo\Core\Metadata\controlled_vocabulary\storage\data_class\ControlledVocabulary[] $controlled_vocabulary
     */
    public function set_controlled_vocabulary($controlled_vocabulary)
    {
        $this->controlled_vocabulary = $controlled_vocabulary;
    }

    /**
     * Returns the controlled vocabulary
     * 
     * @return \Chamilo\Core\Metadata\controlled_vocabulary\storage\data_class\ControlledVocabulary[]
     */
    public function get_controlled_vocabulary()
    {
        return $this->controlled_vocabulary;
    }

    /**
     * Sets the default values
     * 
     * @param \Chamilo\Core\Metadata\value\element\storage\data_class\DefaultElementValue[] $default_values
     */
    public function set_default_values($default_values)
    {
        $this->default_values = $default_values;
    }

    /**
     * Returns the default values
     * 
     * @return \Chamilo\Core\Metadata\value\element\storage\data_class\DefaultElementValue[]
     */
    public function get_default_values()
    {
        return $this->default_values;
    }

    /**
     * Sets if this element is required
     * 
     * @param bool $required
     */
    public function set_required($required)
    {
        $this->required = $required;
    }

    /**
     * Returns if this element is required
     * 
     * @return bool
     */
    public function is_required()
    {
        return $this->required;
    }

    /**
     * Renders the name of this attribute with the prefix of the namespace
     * 
     * @return string
     */
    public function render_name()
    {
        $pref = $this->get_namespace();
        $prefix = (empty($pref)) ? '' : $this->get_namespace() . ':';
        
        return $prefix . $this->get_name();
    }

    /**
     * Moves this object with the display order
     * 
     * @param int $direction
     *
     * @return bool
     */
    public function move($direction)
    {
        $this->set_display_order($this->get_display_order() + $direction);
        
        return $this->update();
    }

    /**
     * **************************************************************************************************************
     * Protected helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the condition for the element nesting dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_element_nesting_dependency_condition()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID), 
            new StaticConditionVariable($this->get_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_CHILD_ELEMENT_ID), 
            new StaticConditionVariable($this->get_id()));
        
        return new OrCondition($conditions);
    }

    /**
     * Returns the condition for the element_rel_attribute dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_element_rel_attribute_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($this->get_id()));
    }

    /**
     * Returns the condition for the element rel content object property dependency
     * 
     * @return \libraries\storage\Condition
     */
    protected function get_default_value_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(), 
                DefaultElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($this->get_id()));
    }

    /**
     * Returns the condition for the element controlled vocabulary dependency
     * 
     * @return EqualityCondition
     */
    protected function get_element_controlled_vocabulary_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ElementControlledVocabulary :: class_name(), 
                ElementControlledVocabulary :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($this->get_id()));
    }

    /**
     * **************************************************************************************************************
     * Display order functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the property for the display order
     * 
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     * 
     * @return Condition
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_SCHEMA_ID));
    }
}
