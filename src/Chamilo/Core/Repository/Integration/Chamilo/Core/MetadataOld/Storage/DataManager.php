<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Storage;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Type\Storage\DataClass\ContentObjectRelMetadataElement;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Storage\DataClass\ContentObjectMetadataAttributeValue;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Storage\DataClass\ContentObjectMetadataElementValue;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * The DataManager for this package
 * 
 * @package repository\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    /**
     * Truncates the metadata values for a given content object
     * 
     * @param int $content_object_id
     */
    public static function truncate_metadata_values_for_content_object($content_object_id)
    {
        $condition = self :: get_element_value_by_content_object_id_condition($content_object_id);
        
        self :: deletes(ContentObjectMetadataElementValue :: class_name(), $condition);
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataAttributeValue :: class_name(), 
                ContentObjectMetadataAttributeValue :: PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($content_object_id));
        
        self :: deletes(ContentObjectMetadataAttributeValue :: class_name(), $condition);
    }

    /**
     * Cleans the metadata values for the given elements and content objects
     * 
     * @param Element[] $elements
     * @param ContentObject[] $content_objects
     *
     * @return bool
     */
    public static function clean_element_values_for_elements_and_content_objects(array $elements, array $content_objects)
    {
        $element_ids = array();
        foreach ($elements as $element)
        {
            $element_ids[] = $element->get_id();
        }
        
        $content_object_ids = array();
        foreach ($content_objects as $content_object)
        {
            $content_object_ids[] = $content_object->get_id();
        }
        
        $conditions = array();
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_ELEMENT_ID), 
            $element_ids);
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_CONTENT_OBJECT_ID), 
            $content_object_ids);
        
        $condition = new AndCondition($conditions);
        
        return self :: deletes(ContentObjectMetadataElementValue :: class_name(), $condition);
    }

    /**
     * Retrieves the element values for a given content object
     * 
     * @param int $content_object_id
     *
     * @return ContentObjectMetadataElementValue[]
     */
    public static function retrieve_element_values_for_content_object_as_array($content_object_id)
    {
        if (! $content_object_id)
        {
            return null;
        }
        
        return self :: retrieves(
            ContentObjectMetadataElementValue :: class_name(), 
            self :: get_element_value_by_content_object_id_condition($content_object_id))->as_array();
    }

    /**
     * Retrieves the element values for a given content object by the fully qualified element name as array
     * 
     * @param int $content_object_id
     *
     * @return array
     */
    public static function retrieve_element_values_for_content_object_with_element_and_schema_as_array(
        $content_object_id)
    {
        if (! $content_object_id)
        {
            return array();
        }
        
        return \Chamilo\Core\MetadataOld\Value\Storage\DataManager :: retrieve_element_values_with_element_and_schema_as_array(
            ContentObjectMetadataElementValue :: class_name(), 
            self :: get_element_value_by_content_object_id_condition($content_object_id));
    }

    /**
     * Returns the condition for the element value by a given content object id
     * 
     * @param int $content_object_id
     *
     * @return Condition
     */
    protected static function get_element_value_by_content_object_id_condition($content_object_id)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($content_object_id));
    }

    /**
     * Sets the dependencies for the given element
     * 
     * @param \core\metadata\element\storage\data_class\Element $element
     * @param array $dependencies
     */
    public static function get_element_dependencies($element, array &$dependencies)
    {
        $dependencies[ContentObjectMetadataElementValue :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element->get_id()));
        
        $dependencies[ContentObjectRelMetadataElement :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectRelMetadataElement :: class_name(), 
                ContentObjectRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID), 
            new StaticConditionVariable($element->get_id()));
        
        $dependencies[ContentObjectPropertyRelMetadataElement :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPropertyRelMetadataElement :: class_name(), 
                ContentObjectPropertyRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID), 
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
        $dependencies[ContentObjectMetadataAttributeValue :: class_name()] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataAttributeValue :: class_name(), 
                ContentObjectMetadataAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
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
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_ELEMENT_ID), 
            new StaticConditionVariable($element_controlled_vocabulary->get_element_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID), 
            new StaticConditionVariable($element_controlled_vocabulary->get_controlled_vocabulary_id()));
        
        $dependencies[ContentObjectMetadataElementValue :: class_name()] = new AndCondition($conditions);
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
                ContentObjectMetadataAttributeValue :: class_name(), 
                ContentObjectMetadataAttributeValue :: PROPERTY_ATTRIBUTE_ID), 
            new StaticConditionVariable($attribute_controlled_vocabulary->get_attribute_id()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectMetadataAttributeValue :: class_name(), 
                ContentObjectMetadataAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID), 
            new StaticConditionVariable($attribute_controlled_vocabulary->get_controlled_vocabulary_id()));
        
        $dependencies[ContentObjectMetadataAttributeValue :: class_name()] = new AndCondition($conditions);
    }
}