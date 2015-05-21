<?php
namespace Chamilo\Core\MetadataOld\Storage;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\AttributeControlledVocabulary;
use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementNesting;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Core\MetadataOld\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class represents the data manager for this package
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package core.metadata
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

    /**
     * **************************************************************************************************************
     * Main Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Deletes the attribute associations from a given element
     * 
     * @param Element $element
     *
     * @return bool
     */
    public static function delete_attribute_associations_from_element($element)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element->get_id()));
        
        return self :: deletes(ElementRelAttribute :: class_name(), $condition);
    }

    /**
     * Deletes the element nestings from a given element
     * 
     * @param Element $element
     *
     * @return bool
     */
    public static function delete_element_nestings_from_metadata_element($element)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID), 
            new StaticConditionVariable($element->get_id()));
        
        return self :: deletes(ElementNesting :: class_name(), $condition);
    }

    /**
     * Retrieves an element by a given schema id and name
     * 
     * @param int $schema_id
     * @param string $name
     *
     * @return mixed
     */
    public static function retrieve_element_by_schema_id_and_name($schema_id, $name)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
            new StaticConditionVariable($schema_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_NAME), 
            new StaticConditionVariable($name));
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(Element :: class_name(), $condition);
    }

    /**
     * Retrieves an attribute by a given schema id and name
     * 
     * @param int $schema_id
     * @param string $name
     *
     * @return mixed
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
     * Retrieves the namespaces of schema's that have elements
     * 
     * @return array
     */
    public static function retrieve_schema_namespaces_with_elements()
    {
        $properties = new DataClassProperties();
        
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: DISTINCT, 
                new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_NAMESPACE)));
        
        $properties->add(new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_ID));
        
        $joins = self :: get_elements_with_schema_joins();
        
        $parameters = new RecordRetrievesParameters($properties, null, null, null, array(), $joins);
        
        $record_result_set = self :: records(Schema :: class_name(), $parameters);
        
        $prefixes = array();
        
        while ($record = $record_result_set->next_result())
        {
            $prefixes[$record[Schema :: PROPERTY_ID]] = $record[Schema :: PROPERTY_NAMESPACE];
        }
        
        return $prefixes;
    }

    /**
     * Retrieves the controlled vocabulary from a given element
     * 
     * @param int $element_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_controlled_vocabulary_from_element($element_id)
    {
        return self :: retrieve_controlled_vocabulary_from_relation(
            ElementControlledVocabulary :: class_name(), 
            self :: get_element_controlled_vocabulary_condition_for_element($element_id));
    }

    /**
     * Deletes the controlled vocabulary for a given element
     * 
     * @param int $element_id
     *
     * @return bool
     */
    public static function delete_controlled_vocabulary_for_element($element_id)
    {
        return self :: deletes(
            ElementControlledVocabulary :: class_name(), 
            self :: get_element_controlled_vocabulary_condition_for_element($element_id));
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
        return self :: retrieve_controlled_vocabulary_from_relation(
            AttributeControlledVocabulary :: class_name(), 
            self :: get_attribute_controlled_vocabulary_condition_for_attribute($attribute_id));
    }

    /**
     * Helper function to retrieve the controlled vocabulary from a given relation
     * 
     * @param string $relation_class
     * @param \libraries\storage\Condition $condition
     *
     * @return \libraries\storage\ResultSet
     */
    protected static function retrieve_controlled_vocabulary_from_relation($relation_class, $condition)
    {
        $joins = new Joins();
        
        $joins->add(
            new Join(
                $relation_class, 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ControlledVocabulary :: class_name(), 
                        ControlledVocabulary :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        $relation_class :: class_name(), 
                        $relation_class :: PROPERTY_CONTROLLED_VOCABULARY_ID))));
        
        $properties = new DataClassRetrievesParameters($condition, null, null, null, $joins);
        
        return self :: retrieves(ControlledVocabulary :: class_name(), $properties);
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
     * Checks if an element has a controlled vocabulary or not
     * 
     * @param int $element_id
     *
     * @return bool
     */
    public static function element_has_controlled_vocabulary($element_id)
    {
        $condition = self :: get_element_controlled_vocabulary_condition_for_element($element_id);
        
        return self :: count(ElementControlledVocabulary :: class_name(), $condition) > 0;
    }

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
     * Returns the condition for element controlled vocabulary for a given element
     * 
     * @param int $element_id
     *
     * @return EqualityCondition
     */
    protected static function get_element_controlled_vocabulary_condition_for_element($element_id)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ElementControlledVocabulary :: class_name(), 
                ElementControlledVocabulary :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
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
     * Retrieves the elements for a given schema
     * 
     * @param int $schema_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_elements_for_schema($schema_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
            new StaticConditionVariable($schema_id));
        
        return self :: retrieves(Element :: class_name(), $condition);
    }

    /**
     * Retrieves the nested elements for a given element
     * 
     * @param int $element_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_nested_elements_for_element($element_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        $joins = new Joins();
        $joins->add(
            new Join(
                ElementNesting :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ElementNesting :: class_name(), 
                        ElementNesting :: PROPERTY_CHILD_ELEMENT_ID), 
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID))));
        
        $properties = new DataClassRetrievesParameters($condition, null, null, array(), $joins);
        
        return self :: retrieves(Element :: class_name(), $properties);
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

    /**
     * Retrieves the default values for a given element
     * 
     * @param int $element_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_default_values_for_element($element_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultElementValue :: class_name(), 
                DefaultElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        return self :: retrieves(DefaultElementValue :: class_name(), $condition);
    }

    /**
     * Retrieves the attributes for the given schema
     * 
     * @param int $schema_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_attributes_for_schema($schema_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Attribute :: class_name(), Attribute :: PROPERTY_SCHEMA_ID), 
            new StaticConditionVariable($schema_id));
        
        return self :: retrieves(Attribute :: class_name(), $condition);
    }

    /**
     * Retrieves the default values for a given attribute
     * 
     * @param int $attribute_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_default_values_for_attribute($attribute_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                DefaultAttributeValue :: class_name(), 
                DefaultAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute_id));
        
        return self :: retrieves(DefaultAttributeValue :: class_name(), $condition);
    }

    /**
     * Retrieves all the element names combined with the schema namespaces as an array
     * 
     * @return string[int]
     */
    public static function retrieve_element_names_with_schema_namespaces()
    {
        $properties = new DataClassProperties();
        
        $properties->add(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_NAME));
        $properties->add(new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_NAMESPACE));
        
        $joins = self :: get_elements_with_schema_joins();
        
        $parameters = new RecordRetrievesParameters($properties, null, null, null, array(), $joins);
        
        $records = self :: records(Schema :: class_name(), $parameters);
        
        $element_names = array();
        
        while ($record = $records->next_result())
        {
            $element_names[$record[Element :: PROPERTY_ID]] = $record[Schema :: PROPERTY_NAMESPACE] . ':' .
                 $record[Element :: PROPERTY_NAME];
        }
        
        return $element_names;
    }

    /**
     * Returns the joins object for the elements and the schema table
     * 
     * @return Joins
     */
    protected static function get_elements_with_schema_joins()
    {
        $joins = new Joins();
        
        $joins->add(
            new Join(
                Element :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
                    new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_ID))));
        
        return $joins;
    }

    /**
     * Retrieves an element by a given fully qualified element name
     * 
     * @param string $fully_qualified_element_name - The namespace and the element name (namespace:element)
     * @return \Chamilo\Core\MetadataOld\element\storage\data_class\Element
     */
    public static function retrieve_element_by_fully_qualified_element_name($fully_qualified_element_name)
    {
        $fully_qualified_element_name_parts = explode(':', $fully_qualified_element_name);
        
        return self :: retrieve_element_by_schema_namespace_and_element_name(
            $fully_qualified_element_name_parts[0], 
            $fully_qualified_element_name_parts[1]);
    }

    /**
     * Retrieves an element in a given schema by the schema namespace and the element name
     * 
     * @param string $namespace
     * @param string $element_name
     *
     * @throws \InvalidArgumentException
     *
     * @return Element
     */
    public static function retrieve_element_by_schema_namespace_and_element_name($namespace, $element_name)
    {
        $schema = \Chamilo\Core\MetadataOld\Schema\Storage\DataManager :: retrieve_schema_by_namespace($namespace);
        $element = self :: retrieve_element_by_schema_id_and_name($schema->get_id(), $element_name);
        
        if (! $element)
        {
            throw new \InvalidArgumentException(
                'The given element name ' . $element_name . ' in namespace ' . $namespace . ' is invalid');
        }
        
        return $element;
    }

    /**
     * Retrieves an attribute by a given fully qualified attribute name
     * 
     * @param string $fully_qualified_attribute_name - The namespace and the attribute name (namespace:attribute)
     * @return \Chamilo\Core\MetadataOld\attribute\Element
     */
    public static function retrieve_attribute_by_fully_qualified_attribute_name($fully_qualified_attribute_name)
    {
        $fully_qualified_attribute_name_parts = explode(':', $fully_qualified_attribute_name);
        
        return self :: retrieve_attribute_by_schema_namespace_and_attribute_name(
            $fully_qualified_attribute_name_parts[0], 
            $fully_qualified_attribute_name_parts[1]);
    }

    /**
     * Retrieves an attribute in a given schema by the schema namespace and the attribute name
     * 
     * @param string $namespace
     * @param string $attribute_name
     *
     * @throws \InvalidArgumentException
     *
     * @return Element
     */
    public static function retrieve_attribute_by_schema_namespace_and_attribute_name($namespace, $attribute_name)
    {
        $schema = \Chamilo\Core\MetadataOld\Schema\Storage\DataManager :: retrieve_schema_by_namespace($namespace);
        $attribute = self :: retrieve_attribute_by_schema_id_and_name($schema->get_id(), $attribute_name);
        
        if (! $attribute)
        {
            throw new \InvalidArgumentException(
                'The given attribute name ' . $attribute_name . ' in namespace ' . $namespace . ' is invalid');
        }
        
        return $attribute;
    }
}