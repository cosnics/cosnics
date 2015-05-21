<?php
namespace Chamilo\Core\MetadataOld\Attribute\Storage\DataClass;

use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\ImplementationNotifierDataClassListener;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes an attribute of the metadata
 * 
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class Attribute extends DataClass
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

    /**
     * **************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    private $ns_prefix = false;

    /**
     * The default values for this element
     * 
     * @var \Chamilo\Core\MetadataOld\value\attribute\DefaultAttributeValue[]
     */
    private $default_values;

    /**
     * The controlled vocabulary for this element
     * 
     * @var \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary[]
     */
    private $controlled_vocabulary;

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
        
        $this->add_listener(
            new ImplementationNotifierDataClassListener(
                $this, 
                \Chamilo\Core\MetadataOld\Manager :: context(), 
                array(ImplementationNotifierDataClassListener :: GET_DEPENDENCIES => 'get_attribute_dependencies')));
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
                ElementRelAttribute :: class_name() => $this->get_element_rel_attribute_dependency_condition(), 
                AttributeControlledVocabulary :: class_name() => $this->get_attribute_controlled_vocabulary_dependency_condition(), 
                DefaultAttributeValue :: class_name() => $this->get_default_attribute_value_dependency_condition()));
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the schema_id
     * 
     * @return int - the schema_id.
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
     * @return string - the name.
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
        if (! $this->ns_prefix)
        {
            $schema = \Chamilo\Core\MetadataOld\Schema\Storage\DataManager :: retrieve_by_id(
                Schema :: class_name(), 
                $this->get_schema_id());
            
            if (! $schema)
            {
                return false;
            }
            
            $this->set_namespace($schema->get_namespace());
        }
        
        return $this->ns_prefix;
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
     * Sets the prefix of the schema namespace
     * 
     * @param string $ns_prefix
     */
    public function set_namespace($ns_prefix)
    {
        $this->ns_prefix = $ns_prefix;
    }

    /**
     * Sets the controlled vocabulary
     * 
     * @param \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary[] $controlled_vocabulary
     */
    public function set_controlled_vocabulary($controlled_vocabulary)
    {
        $this->controlled_vocabulary = $controlled_vocabulary;
    }

    /**
     * Returns the controlled vocabulary
     * 
     * @return \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary[]
     */
    public function get_controlled_vocabulary()
    {
        return $this->controlled_vocabulary;
    }

    /**
     * Sets the default values
     * 
     * @param \Chamilo\Core\MetadataOld\value\attribute\DefaultAttributeValue[] $default_values
     */
    public function set_default_values($default_values)
    {
        $this->default_values = $default_values;
    }

    /**
     * Returns the default values
     * 
     * @return \Chamilo\Core\MetadataOld\value\attribute\DefaultAttributeValue[]
     */
    public function get_default_values()
    {
        return $this->default_values;
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
    protected function get_element_rel_attribute_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($this->get_id()));
    }

    /**
     * Returns the condition for the attribute controlled vocabulary dependency
     * 
     * @return EqualityCondition
     */
    protected function get_attribute_controlled_vocabulary_dependency_condition()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                AttributeControlledVocabulary :: class_name(), 
                AttributeControlledVocabulary :: PROPERTY_ATTRIBUTE_ID), 
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
                DefaultAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($this->get_id()));
    }
}