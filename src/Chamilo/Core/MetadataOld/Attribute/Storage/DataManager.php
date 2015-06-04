<?php
namespace Chamilo\Core\MetadataOld\Attribute\Storage;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\AttributeControlledVocabulary;
use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     * Checks if an attribute has a controlled vocabulary or not
     * 
     * @param int $attribute_id
     *
     * @return bool
     */
    public static function attribute_has_controlled_vocabulary($attribute_id)
    {
        $condition = self :: get_attribute_controlled_vocabulary_condition_for_attribute($attribute_id);
        
        return self :: count(AttributeControlledVocabulary :: class_name(), $condition) > 0;
    }

    /**
     * Returns the condition for attribute controlled vocabulary for a given attribute
     * 
     * @param int $attribute_id
     *
     * @return EqualityCondition
     */
    protected static function get_attribute_controlled_vocabulary_condition_for_attribute($attribute_id)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                AttributeControlledVocabulary :: class_name(), 
                AttributeControlledVocabulary :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute_id));
    }

    /**
     * Retrieves the controlled vocabulary from a given attribute
     * 
     * @param int $attribute_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_controlled_vocabulary_from_attribute($attribute_id)
    {
        $joins = new Joins();
        
        $joins->add(
            new Join(
                AttributeControlledVocabulary :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ControlledVocabulary :: class_name(), 
                        ControlledVocabulary :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        AttributeControlledVocabulary :: class_name(), 
                        AttributeControlledVocabulary :: PROPERTY_CONTROLLED_VOCABULARY_ID))));
        
        $condition = self :: get_attribute_controlled_vocabulary_condition_for_attribute($attribute_id);
        
        $properties = new DataClassRetrievesParameters($condition, null, null, null, $joins);
        
        return self :: retrieves(ControlledVocabulary :: class_name(), $properties);
    }

    /**
     * Retrieves an array of controlled vocabulary terms for a given attribute
     * 
     * @param int $attribute_id
     *
     * @return array
     */
    public static function retrieve_controlled_vocabulary_terms_from_attribute($attribute_id)
    {
        $terms = array();
        
        $controlled_vocabulary = self :: retrieve_controlled_vocabulary_from_attribute($attribute_id);
        while ($controlled_vocabulary_term = $controlled_vocabulary->next_result())
        {
            $terms[$controlled_vocabulary_term->get_id()] = $controlled_vocabulary_term->get_value();
        }
        
        return $terms;
    }

    /**
     * Deletes the controlled vocabulary for a given attribute
     * 
     * @param int $attribute_id
     *
     * @return bool
     */
    public static function delete_controlled_vocabulary_for_attribute($attribute_id)
    {
        return self :: deletes(
            AttributeControlledVocabulary :: class_name(), 
            self :: get_attribute_controlled_vocabulary_condition_for_attribute($attribute_id));
    }

    /**
     * Retrieves an attribute for a given schema and name
     * 
     * @param int $schema_id
     * @param string $name
     *
     * @return Attribute
     */
    public static function retrieve_attribute_by_schema_id_and_name($schema_id, $name)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Attribute :: class_name(), Attribute :: PROPERTY_SCHEMA_ID), 
            new StaticConditionVariable($schema_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Attribute :: class_name(), Attribute :: PROPERTY_NAME), 
            new StaticConditionVariable($name));
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(Attribute :: class_name(), $condition);
    }

    /**
     * Retrieves the attribute controlled vocabulary by a given attribute and controlled vocabulary
     * 
     * @param int $attribute_id
     * @param int $controlled_vocabulary_id
     *
     * @return AttributeControlledVocabulary
     */
    public static function retrieve_attribute_controlled_vocabulary_by_attribute_and_controlled_vocabulary($attribute_id, 
        $controlled_vocabulary_id)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AttributeControlledVocabulary :: class_name(), 
                AttributeControlledVocabulary :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                AttributeControlledVocabulary :: class_name(), 
                AttributeControlledVocabulary :: PROPERTY_CONTROLLED_VOCABULARY_ID), 
            new StaticConditionVariable($controlled_vocabulary_id));
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(AttributeControlledVocabulary :: class_name(), $condition);
    }

    /**
     * Retrieves the attributes for a given element
     * 
     * @param int $element_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_attributes_for_element($element_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        $joins = new Joins();
        $joins->add(
            new Join(
                ElementRelAttribute :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ElementRelAttribute :: class_name(), 
                        ElementRelAttribute :: PROPERTY_ATTRIBUTE_ID), 
                    new PropertyConditionVariable(Attribute :: class_name(), Attribute :: PROPERTY_ID))));
        
        $properties = new DataClassRetrievesParameters($condition, null, null, array(), $joins);
        
        return self :: retrieves(Attribute :: class_name(), $properties);
    }
}