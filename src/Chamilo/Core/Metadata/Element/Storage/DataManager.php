<?php
namespace Chamilo\Core\Metadata\Element\Storage;

use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementControlledVocabulary;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementNesting;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\Metadata\Value\Storage\DataClass\ElementValue;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'metadata_';

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

    public static function get_display_order_total_for_schema($schema_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
            new StaticConditionVariable($schema_id));
        
        return DataManager :: count(Element :: class_name(), $condition);
    }

    /**
     * Deletes the element nestings from a given element
     * 
     * @param Element $element
     *
     * @return bool
     */
    public static function delete_element_nestings_from_element($element)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID), 
            new StaticConditionVariable($element->get_id()));
        
        return self :: deletes(ElementNesting :: class_name(), $condition);
    }

    /**
     * Deletes the attribute associations from a given element
     * 
     * @param Element $element
     *
     * @return bool
     */
    public static function delete_element_rel_attributes_from_element($element)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element->get_id()));
        
        return self :: deletes(ElementRelAttribute :: class_name(), $condition);
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
        $joins = new Joins();
        
        $joins->add(
            new Join(
                ElementControlledVocabulary :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ControlledVocabulary :: class_name(), 
                        ControlledVocabulary :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        ElementControlledVocabulary :: class_name(), 
                        ElementControlledVocabulary :: PROPERTY_CONTROLLED_VOCABULARY_ID))));
        
        $condition = self :: get_element_controlled_vocabulary_condition_for_element($element_id);
        
        $properties = new DataClassRetrievesParameters($condition, null, null, null, $joins);
        
        return self :: retrieves(ControlledVocabulary :: class_name(), $properties);
    }

    /**
     * Retrieves an array of controlled vocabulary terms for a given element
     * 
     * @param int $element_id
     *
     * @return array
     */
    public static function retrieve_controlled_vocabulary_terms_from_element($element_id)
    {
        $terms = array();
        
        $controlled_vocabulary = self :: retrieve_controlled_vocabulary_from_element($element_id);
        while ($controlled_vocabulary_term = $controlled_vocabulary->next_result())
        {
            $terms[$controlled_vocabulary_term->get_id()] = $controlled_vocabulary_term->get_value();
        }
        
        return $terms;
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
     * Retrieves the element controlled vocabulary by a given element and controlled vocabulary
     * 
     * @param int $element_id
     * @param int $controlled_vocabulary_id
     *
     * @return ElementControlledVocabulary
     */
    public static function retrieve_element_controlled_vocabulary_by_element_and_controlled_vocabulary($element_id, 
        $controlled_vocabulary_id)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ElementControlledVocabulary :: class_name(), 
                ElementControlledVocabulary :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ElementControlledVocabulary :: class_name(), 
                ElementControlledVocabulary :: PROPERTY_CONTROLLED_VOCABULARY_ID), 
            new StaticConditionVariable($controlled_vocabulary_id));
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(ElementControlledVocabulary :: class_name(), $condition);
    }

    /**
     * Retrieves the element controlled vocabulary by a given element and controlled vocabulary
     * 
     * @param $parent_element_id
     * @param $child_element_id
     * @return ElementNesting
     */
    public static function retrieve_element_nesting_by_parent_and_child_element($parent_element_id, $child_element_id)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID), 
            new StaticConditionVariable($parent_element_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_CHILD_ELEMENT_ID), 
            new StaticConditionVariable($child_element_id));
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(ElementNesting :: class_name(), $condition);
    }

    /**
     * Retrieves the element controlled vocabulary by a given element and controlled vocabulary
     * 
     * @param int $element_id
     * @param int $attribute_id
     *
     * @return ElementRelAttribute
     */
    public static function retrieve_element_rel_attribute_by_element_and_attribute($element_id, $attribute_id)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ElementRelAttribute :: class_name(), 
                ElementRelAttribute :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute_id));
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(ElementRelAttribute :: class_name(), $condition);
    }

    /**
     * Retrieves an element by a given fully qualified element name
     * 
     * @param string $fully_qualified_element_name - The namespace and the element name (namespace:element)
     * @return \Chamilo\Core\Metadata\element\storage\data_class\Element
     */
    public static function retrieve_element_by_fully_qualified_element_name($fully_qualified_element_name)
    {
        $fully_qualified_element_name_parts = explode(':', $fully_qualified_element_name);
        
        return \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_element_by_schema_namespace_and_element_name(
            $fully_qualified_element_name_parts[0], 
            $fully_qualified_element_name_parts[1]);
    }

    /**
     * Creates a given element value object with the given data
     * 
     * @param ElementValue $element_value_object
     * @param string $fully_qualified_element_name
     * @param mixed $value
     *
     * @throws \Exception
     */
    public static function create_element_value_by_fully_qualified_element_name_and_value(
        ElementValue $element_value_object, $fully_qualified_element_name, $value)
    {
        if (! is_numeric($value) && empty($value))
        {
            return;
        }
        
        $element = self :: retrieve_element_by_fully_qualified_element_name($fully_qualified_element_name);
        $has_controlled_vocabulary = self :: element_has_controlled_vocabulary($element->get_id());
        
        if ($has_controlled_vocabulary && $value == 0)
        {
            return;
        }
        
        $element_value_object->set_element_id($element->get_id());
        
        if ($has_controlled_vocabulary)
        {
            $element_value_object->set_element_vocabulary_id($value);
        }
        else
        {
            $element_value_object->set_value($value);
        }
        
        if (! $element_value_object->create())
        {
            throw new \Exception(
                Translation :: get(
                    'ObjectNotCreated', 
                    array('OBJECT' => Translation :: get('ElementValue', null, 'core\metadata')), 
                    Utilities :: COMMON_LIBRARIES));
        }
    }

    /**
     * Returns the elements for a given schema
     * 
     * @param int $schema_id
     *
     * @return ResultSet
     */
    public static function retrieve_elements_for_schema($schema_id)
    {
        return self :: retrieves(Element :: class_name(), self :: get_elements_for_schema_condition($schema_id));
    }

    /**
     * Counts the elements for a given schema
     * 
     * @param int $schema_id
     *
     * @return int
     */
    public static function count_elements_for_schema($schema_id)
    {
        return self :: count(Element :: class_name(), self :: get_elements_for_schema_condition($schema_id));
    }

    /**
     * Returns the condition for all the elements of a given schema
     * 
     * @param int $schema_id
     *
     * @return \libraries\storage\Condition
     */
    protected function get_elements_for_schema_condition($schema_id)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
            new StaticConditionVariable($schema_id));
    }

    /**
     * Retrieves all the elements that either do not have children, or are parent elements of a given schema
     * 
     * @param int $schema_id
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_parent_elements_from_schema($schema_id)
    {
        $conditions = array();
        
        $conditions[] = self :: get_elements_for_schema_condition($schema_id);
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_PARENT_ELEMENT_ID), 
            null);
        
        $condition = new AndCondition($conditions);
        
        $joins = new Joins();
        
        $joins->add(
            new Join(
                ElementNesting :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        ElementNesting :: class_name(), 
                        ElementNesting :: PROPERTY_CHILD_ELEMENT_ID)), 
                Join :: TYPE_LEFT));
        
        $parameters = new DataClassRetrievesParameters($condition, null, null, array(), $joins);
        
        return self :: retrieves(Element :: class_name(), $parameters);
    }

    /**
     * Retrieves the nested elements for the given element
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
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        ElementNesting :: class_name(), 
                        ElementNesting :: PROPERTY_CHILD_ELEMENT_ID))));
        
        $parameters = new DataClassRetrievesParameters($condition, null, null, array(), $joins);
        
        return self :: retrieves(Element :: class_name(), $parameters);
    }

    /**
     * Returns an array with the parent element ids of the given element
     * 
     * @param $element_id
     * @param bool $recursive
     *
     * @return int[]
     */
    public static function retrieve_parent_element_ids($element_id, $recursive = true)
    {
        $parent_element_ids = array();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ElementNesting :: class_name(), ElementNesting :: PROPERTY_CHILD_ELEMENT_ID), 
            new StaticConditionVariable($element_id));
        
        $element_nestings = self :: retrieves(ElementNesting :: class_name(), $condition);
        
        while ($element_nesting = $element_nestings->next_result())
        {
            $parent_element_id = $element_nesting->get_parent_element_id();
            $parent_element_ids[] = $parent_element_id;
            
            if ($recursive)
            {
                $parent_element_ids = array_merge(
                    $parent_element_ids, 
                    self :: retrieve_parent_element_ids($parent_element_id));
            }
        }
        
        return $parent_element_ids;
    }
}