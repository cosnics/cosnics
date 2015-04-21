<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Type\Storage;

use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Type\Storage\DataClass\ContentObjectRelMetadataElement;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * The DataManager for this package
 * 
 * @package repository\integration\core\metadata\linker\type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    /**
     * Retrieves the ContentObjectRelMetadataElements from the given content object type
     * 
     * @param string $content_object_type
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_content_object_rel_metadata_elements_by_content_object_type(
        $content_object_type = null)
    {
        $condition_value = $content_object_type ? new StaticConditionVariable($content_object_type) : null;
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectRelMetadataElement :: class_name(), 
                ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE), 
            $condition_value);
        
        return self :: retrieves(ContentObjectRelMetadataElement :: class_name(), $condition);
    }

    /**
     * Returns the metadata elements as an array for the given content object type
     * 
     * @param string $content_object_type
     *
     * @return array
     */
    public static function retrieve_metadata_elements_for_content_object_type($content_object_type)
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertiesConditionVariable(Element :: class_name()));
        $properties->add(
            new PropertyConditionVariable(
                ContentObjectRelMetadataElement :: class_name(), 
                ContentObjectRelMetadataElement :: PROPERTY_REQUIRED));
        
        $joins = new Joins();
        
        $joins->add(
            new Join(
                ContentObjectRelMetadataElement :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        ContentObjectRelMetadataElement :: class_name(), 
                        ContentObjectRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID))))

        ;
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectRelMetadataElement :: class_name(), 
                ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE), 
            null);
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectRelMetadataElement :: class_name(), 
                ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE), 
            new StaticConditionVariable($content_object_type));
        
        $condition = new OrCondition($conditions);
        
        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins);
        
        $elements = array();
        
        $records = \Chamilo\Core\MetadataOld\Storage\DataManager :: records(Element :: class_name(), $parameters);
        while ($record = $records->next_result())
        {
            $required = $record[ContentObjectRelMetadataElement :: PROPERTY_REQUIRED];
            unset($record[ContentObjectRelMetadataElement :: PROPERTY_REQUIRED]);
            
            $element = new Element($record);
            $element->set_required($required);
            
            $elements[] = $element;
        }
        
        return $elements;
    }

    /**
     * Selects the metadata elements that are connected to the given content objects, depending on their type
     * 
     * @param ContentObject[] $content_objects
     *
     * @return Element[]
     */
    public static function get_common_metadata_elements(array $content_objects)
    {
        $content_object_types = array();
        
        foreach ($content_objects as $content_object)
        {
            $context = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($content_object->get_type());
            if (! in_array($context, $content_object_types))
            {
                $content_object_types[] = $context;
            }
        }
        
        $element_ids_per_context = array();
        
        foreach ($content_object_types as $content_object_type)
        {
            $content_object_rel_metadata_elements = self :: retrieve_content_object_rel_metadata_elements_by_content_object_type(
                $content_object_type);
            
            $element_ids_per_context[$content_object_type] = array();
            
            while ($content_object_rel_metadata_element = $content_object_rel_metadata_elements->next_result())
            {
                $element_ids_per_context[$content_object_type][] = $content_object_rel_metadata_element->get_metadata_element_id();
            }
        }
        
        $intersected_elements = array();
        
        $counter = 0;
        
        foreach ($element_ids_per_context as $element_ids)
        {
            if ($counter == 0)
            {
                $intersected_elements = $element_ids;
            }
            
            else
            {
                $intersected_elements = array_intersect($intersected_elements, $element_ids);
            }
            
            $counter ++;
        }
        
        $all_content_objects_metadata_elements = self :: retrieve_content_object_rel_metadata_elements_by_content_object_type();
        
        while ($content_object_rel_metadata_element = $all_content_objects_metadata_elements->next_result())
        {
            $intersected_elements[] = $content_object_rel_metadata_element->get_metadata_element_id();
        }
        
        $elements = \Chamilo\Core\MetadataOld\Storage\DataManager :: retrieves(
            Element :: class_name(), 
            new InCondition(
                new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                $intersected_elements))->as_array();
        
        return $elements;
    }
}