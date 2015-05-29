<?php
namespace Chamilo\Core\MetadataOld\Value\Storage;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     * Retrieves the element values for a given element value class by the fully qualified element name as array
     * 
     * @param string $element_value_class
     * @param \libraries\storage\Condition $condition
     *
     * @return array
     */
    public static function retrieve_element_values_with_element_and_schema_as_array($element_value_class, 
        $condition = null)
    {
        $controlled_vocabulary_value_alias = 'controlled_vocabulary_value';
        
        $joins = new Joins();
        
        $joins->add(
            new Join(
                $element_value_class :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        $element_value_class :: class_name(), 
                        $element_value_class :: PROPERTY_ELEMENT_ID))));
        
        $joins->add(
            new Join(
                Schema :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
                    new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_ID))));
        
        $joins->add(
            new Join(
                ControlledVocabulary :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        $element_value_class :: class_name(), 
                        $element_value_class :: PROPERTY_ELEMENT_VOCABULARY_ID), 
                    new PropertyConditionVariable(
                        ControlledVocabulary :: class_name(), 
                        ControlledVocabulary :: PROPERTY_ID)), 
                Join :: TYPE_LEFT));
        
        $properties = new DataClassProperties();
        
        $properties->add(
            new PropertyConditionVariable($element_value_class :: class_name(), $element_value_class :: PROPERTY_VALUE));
        
        $properties->add(
            new FixedPropertyConditionVariable(
                ControlledVocabulary :: class_name(), 
                ControlledVocabulary :: PROPERTY_VALUE, 
                $controlled_vocabulary_value_alias));
        
        $properties->add(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_NAME));
        $properties->add(new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_NAMESPACE));
        
        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins);
        
        $records = self :: records(Element :: class_name(), $parameters);
        
        $element_values = array();
        
        while ($record = $records->next_result())
        {
            $value = $record[$controlled_vocabulary_value_alias] ? $record[$controlled_vocabulary_value_alias] : $record[$element_value_class :: PROPERTY_VALUE];
            
            $element_values[$record[Schema :: PROPERTY_NAMESPACE] . ':' . $record[Element :: PROPERTY_NAME]] = $value;
        }
        
        return $element_values;
    }
}