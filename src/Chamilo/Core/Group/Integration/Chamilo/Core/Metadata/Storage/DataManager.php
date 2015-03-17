<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Metadata\Storage;

use Chamilo\Core\Group\Integration\Chamilo\Core\Metadata\Storage\DataClass\MetadataAttributeValue;
use Chamilo\Core\Group\Integration\Chamilo\Core\Metadata\Storage\DataClass\MetadataElementValue;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * The DataManager for this package
 * 
 * @package group\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'group_';
    const ALIAS_ELEMENT_VALUE_ID = 'id_element_value';
    const ALIAS_ATTRIBUTE_VALUE_ID = 'id_attribute_value';
    const ALIAS_ELEMENT_VALUE = 'value_element_value';
    const ALIAS_ATTRIBUTE_VALUE = 'value_attribute_value';

    /**
     * Sets the dependencies for the given element
     * 
     * @param \core\metadata\element\storage\data_class\Element $element
     * @param array $dependencies
     */
    public static function get_element_dependencies($element, array &$dependencies)
    {
        $dependencies[MetadataElementValue :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataElementValue :: class_name(), 
                MetadataElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element->get_id()));
    }

    /**
     * Sets the dependencies for the given attribute
     * 
     * @param \core\metadata\attribute\storage\data_class\Attribute $attribute
     * @param array $dependencies
     */
    public static function get_attribute_dependencies($attribute, array &$dependencies)
    {
        $dependencies[MetadataAttributeValue :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataAttributeValue :: class_name(), 
                MetadataAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute->get_id()));
    }

    /**
     * Sets the dependencies for the controlled vocabulary elements
     * 
     * @param \core\metadata\element\storage\data_class\ElementControlledVocabulary $element_controlled_vocabulary
     * @param array $dependencies
     */
    public static function get_element_controlled_vocabulary_dependencies($element_controlled_vocabulary, 
        array &$dependencies)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataElementValue :: class_name(), 
                MetadataElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_controlled_vocabulary->get_element_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataElementValue :: class_name(), 
                MetadataElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID), 
            new StaticConditionVariable($element_controlled_vocabulary->get_controlled_vocabulary_id()));
        
        $dependencies[MetadataElementValue :: class_name()] = new AndCondition($conditions);
    }

    /**
     * Sets the dependencies for the controlled vocabulary elements
     * 
     * @param \core\metadata\attribute\storage\data_class\AttributeControlledVocabulary $attribute_controlled_vocabulary
     * @param array $dependencies
     */
    public static function get_attribute_controlled_vocabulary_dependencies($attribute_controlled_vocabulary, 
        array &$dependencies)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataAttributeValue :: class_name(), 
                MetadataAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute_controlled_vocabulary->get_attribute_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataAttributeValue :: class_name(), 
                MetadataAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID), 
            new StaticConditionVariable($attribute_controlled_vocabulary->get_controlled_vocabulary_id()));
        
        $dependencies[MetadataAttributeValue :: class_name()] = new AndCondition($conditions);
    }

    /**
     * Returns the element and attribute values for the given group
     * 
     * @param int $group_id
     *
     * @return \core\group\integration\core\metadata\ElementValue[]
     */
    public static function get_element_and_attribute_values_for_group($group_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataElementValue :: class_name(), 
                MetadataElementValue :: PROPERTY_GROUP_ID), 
            new StaticConditionVariable($group_id));
        
        $property_names = array();
        
        $property_names[MetadataElementValue :: class_name()] = array(
            MetadataElementValue :: PROPERTY_ELEMENT_ID => null, 
            MetadataElementValue :: PROPERTY_GROUP_ID => null, 
            MetadataElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID => null, 
            MetadataElementValue :: PROPERTY_ID => self :: ALIAS_ELEMENT_VALUE_ID, 
            MetadataElementValue :: PROPERTY_VALUE => self :: ALIAS_ELEMENT_VALUE);
        
        $property_names[MetadataAttributeValue :: class_name()] = array(
            MetadataAttributeValue :: PROPERTY_ELEMENT_VALUE_ID => null, 
            MetadataAttributeValue :: PROPERTY_ATTRIBUTE_ID => null, 
            MetadataAttributeValue :: PROPERTY_GROUP_ID => null, 
            MetadataAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID => null, 
            MetadataAttributeValue :: PROPERTY_ID => self :: ALIAS_ATTRIBUTE_VALUE_ID, 
            MetadataAttributeValue :: PROPERTY_VALUE => self :: ALIAS_ATTRIBUTE_VALUE);
        
        $properties = self :: get_dataclass_properties($property_names);
        
        $joins = new Joins();
        
        $joins->add(
            new Join(
                MetadataAttributeValue :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        MetadataElementValue :: class_name(), 
                        MetadataElementValue :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        MetadataAttributeValue :: class_name(), 
                        MetadataAttributeValue :: PROPERTY_ELEMENT_VALUE_ID)), 
                Join :: TYPE_LEFT));
        
        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins);
        
        $records = self :: records(MetadataElementValue :: class_name(), $parameters);
        
        return self :: map_records_to_element_value_objects($records);
    }

    /**
     * Creates a dataclassproperties object with a simple array of property names (to reduce code)
     * 
     * @param array $property_names
     *
     * @return DataClassProperties
     */
    protected static function get_dataclass_properties($property_names)
    {
        $data_class_properties = new DataClassProperties();
        
        foreach ($property_names as $class_name => $properties)
        {
            foreach ($properties as $property => $alias)
            {
                if (! empty($alias))
                {
                    $condition_variable = new FixedPropertyConditionVariable($class_name, $property, $alias);
                }
                else
                {
                    $condition_variable = new PropertyConditionVariable($class_name, $property);
                }
                
                $data_class_properties->add($condition_variable);
            }
        }
        
        return $data_class_properties;
    }

    /**
     * Maps the given records array to element value objects
     * 
     * @param \libraries\storage\ResultSet $records
     *
     * @return ElementValue[]
     */
    protected function map_records_to_element_value_objects($records)
    {
        $element_values = array();
        
        while ($record = $records->next_result())
        {
            $element_value_id = $record[self :: ALIAS_ELEMENT_VALUE_ID];
            
            if (! array_key_exists($element_value_id, $element_values))
            {
                $element_value = new MetadataElementValue($record);
                
                $element_value->set_id($element_value_id);
                $element_value->set_value($record[self :: ALIAS_ELEMENT_VALUE]);
                
                $element_values[$element_value_id] = $element_value;
            }
            else
            {
                $element_value = $element_values[$element_value_id];
            }
            
            $attribute_value_id = $record[self :: ALIAS_ATTRIBUTE_VALUE_ID];
            
            if ($attribute_value_id)
            {
                $attribute_value = new MetadataAttributeValue($record);
                $attribute_value->set_id($attribute_value_id);
                $attribute_value->set_value($record[self :: ALIAS_ATTRIBUTE_VALUE]);
                
                $element_value->add_attribute_value($attribute_value);
            }
        }
        
        return $element_values;
    }

    /**
     * Truncates the metdata values for the given group
     * 
     * @param int $group_id
     *
     * @return bool
     */
    public function truncate_metadata_values_for_group($group_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataElementValue :: class_name(), 
                MetadataElementValue :: PROPERTY_GROUP_ID), 
            new StaticConditionVariable($group_id));
        
        if (! self :: deletes(MetadataElementValue :: class_name(), $condition))
        {
            return false;
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                MetadataAttributeValue :: class_name(), 
                MetadataAttributeValue :: PROPERTY_GROUP_ID), 
            new StaticConditionVariable($group_id));
        
        return self :: deletes(MetadataAttributeValue :: class_name(), $condition);
    }
}