<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataClass\ContentObjectAlternative;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Storage\DataClass\ContentObjectMetadataElementValue;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * The DataManager for this package
 * 
 * @package repository\integration\core\metadata\linker\alternative
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    /**
     * Retrieves the alternative content objects for the given content object
     * 
     * @param int $content_object_id
     *
     * @param \libraries\storage\Condition $condition
     * @param int $count
     * @param int $offset
     * @param array $order_by
     * @param $include_original_object - Optionally include the original content object in the list
     * @return \libraries\storage\RecordResultSet
     */
    public static function retrieve_alternative_content_objects($content_object_id, $condition = null, $count = null, 
        $offset = null, $order_by = array(), $include_original_object = false)
    {
        $properties = new DataClassProperties();
        
        $properties->add(new PropertiesConditionVariable(ContentObjectAlternative :: class_name()));
        $properties->add(new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        
        $properties->add(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
        
        $properties->add(new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE));
        $properties->add(new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_DISPLAY_NAME));
        
        $properties->add(
            new PropertyConditionVariable(
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_VALUE));
        
        $properties->add(
            new PropertyConditionVariable(
                ContentObjectMetadataElementValue :: class_name(), 
                ContentObjectMetadataElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID));
        
        $parameters = new RecordRetrievesParameters(
            $properties, 
            self :: get_alternative_content_objects_condition($content_object_id, $condition, $include_original_object), 
            $count, 
            $offset, 
            $order_by, 
            self :: get_alternative_content_object_joins());
        
        return self :: records(ContentObjectAlternative :: class_name(), $parameters);
    }

    /**
     * Counts the alternative content objects for a given content object
     * 
     * @param int $content_object_id
     * @param \libraries\storage\Condition $condition
     * @param bool $include_original_object
     *
     * @return int
     */
    public static function count_alternative_content_objects($content_object_id, $condition = null, 
        $include_original_object = false)
    {
        $parameters = new DataClassCountParameters(
            self :: get_alternative_content_objects_condition($content_object_id, $condition, $include_original_object), 
            self :: get_alternative_content_object_joins());
        
        return self :: count(ContentObjectAlternative :: class_name(), $parameters);
    }

    /**
     * Retrieves the link numbers for a given content object
     * 
     * @param int $content_object_id
     *
     * @return array
     */
    public static function retrieve_link_numbers_for_content_object($content_object_id)
    {
        $properties = new DataClassProperties();
        
        $properties->add(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_LINK_NUMBER));
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($content_object_id));
        
        $parameters = new RecordRetrievesParameters($properties, $condition);
        
        $records = self :: records(ContentObjectAlternative :: class_name(), $parameters);
        
        $link_numbers = array();
        
        while ($record = $records->next_result())
        {
            $link_numbers[] = $record[ContentObjectAlternative :: PROPERTY_LINK_NUMBER];
        }
        
        return $link_numbers;
    }

    /**
     * Retrieves the link number for the given content object and metadata element
     * 
     * @param int $content_object_id
     * @param int $metadata_element_id
     *
     * @return int
     */
    public static function get_link_number_for_content_object_and_metadata_element($content_object_id, 
        $metadata_element_id)
    {
        $properties = new DataClassProperties();
        
        $properties->add(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_LINK_NUMBER));
        
        $parameters = new RecordRetrieveParameters(
            $properties, 
            self :: get_content_object_alternative_by_content_object_and_metadata_element_condition(
                $content_object_id, 
                $metadata_element_id));
        
        try
        {
            $record = self :: record(ContentObjectAlternative :: class_name(), $parameters);
            return $record[ContentObjectAlternative :: PROPERTY_LINK_NUMBER];
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }

    /**
     * Retrieves a single content object alternative by a given content object and metadata element id
     * 
     * @param int $content_object_id
     * @param int $metadata_element_id
     *
     * @return ContentObjectAlternative
     */
    public static function retrieve_content_object_alternative_by_content_object_and_metadata_element($content_object_id, 
        $metadata_element_id)
    {
        return self :: retrieve(
            ContentObjectAlternative :: class_name(), 
            self :: get_content_object_alternative_by_content_object_and_metadata_element_condition(
                $content_object_id, 
                $metadata_element_id));
    }

    /**
     * Returns the condition for the content object alternative with the given content object id and metadata element id
     * 
     * @param int $content_object_id
     * @param int $metadata_element_id
     *
     * @return Condition
     */
    protected static function get_content_object_alternative_by_content_object_and_metadata_element_condition(
        $content_object_id, $metadata_element_id)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($content_object_id));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_METADATA_ELEMENT_ID), 
            new StaticConditionVariable($metadata_element_id));
        
        return new AndCondition($conditions);
    }

    /**
     * Updates all the alternatives with a given link number to a new link number
     * 
     * @param int $old_link_number
     * @param int $new_link_number
     *
     * @return bool
     */
    public static function update_link_number($old_link_number, $new_link_number)
    {
        $properties = new DataClassProperties();
        
        $properties->add(
            new DataClassProperty(
                new PropertyConditionVariable(
                    ContentObjectAlternative :: class_name(), 
                    ContentObjectAlternative :: PROPERTY_LINK_NUMBER), 
                new StaticConditionVariable($new_link_number)));
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_LINK_NUMBER), 
            new StaticConditionVariable($old_link_number));
        
        return self :: updates(ContentObjectAlternative :: class_name(), $properties, $condition);
    }

    /**
     * Count the number of content object alternatives records for a given link number
     * 
     * @param int $link_number
     *
     * @return int
     */
    public static function count_content_object_alternatives_by_link_number($link_number)
    {
        return self :: count(
            ContentObjectAlternative :: class_name(), 
            self :: get_content_object_alternatives_by_link_number_condition($link_number));
    }

    /**
     * Deletes the content object alternatives by a given link number
     * 
     * @param int $link_number
     *
     * @return bool
     */
    public static function delete_content_object_alternatives_by_link_number($link_number)
    {
        return self :: deletes(
            ContentObjectAlternative :: class_name(), 
            self :: get_content_object_alternatives_by_link_number_condition($link_number));
    }

    /**
     * Returns the condition for the content objects by a given link number
     * 
     * @param int $link_number
     *
     * @return EqualityCondition
     */
    protected static function get_content_object_alternatives_by_link_number_condition($link_number)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_LINK_NUMBER), 
            new StaticConditionVariable($link_number));
    }

    /**
     * Creates the content object alternatives for the given base content object, link content objects and metadata
     * element
     * 
     * @param int $base_content_object_id
     * @param array $selected_content_object_ids
     * @param int $metadata_element_id
     *
     * @return bool
     */
    public static function create_content_object_alternatives($base_content_object_id, $selected_content_object_ids, 
        $metadata_element_id)
    {
        $link_number = DataManager :: get_link_number_for_content_object_and_metadata_element(
            $base_content_object_id, 
            $metadata_element_id);
        
        if (! $link_number)
        {
            $link_number = self :: retrieve_next_value(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_LINK_NUMBER);
            
            if (! self :: create_content_object_alternative($base_content_object_id, $link_number, $metadata_element_id))
            {
                return false;
            }
        }
        
        foreach ($selected_content_object_ids as $selected_content_object_id)
        {
            $existing_link_number = self :: get_link_number_for_content_object_and_metadata_element(
                $selected_content_object_id, 
                $metadata_element_id);
            
            if ($existing_link_number)
            {
                $success = self :: update_link_number($existing_link_number, $link_number);
            }
            else
            {
                $success = self :: create_content_object_alternative(
                    $selected_content_object_id, 
                    $link_number, 
                    $metadata_element_id);
            }
            
            if (! $success)
            {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Updates the given content object alternative to a new metadata element by removing the alternative and creating a
     * new alternative with the given base content object and metadata element
     * 
     * @param ContentObjectAlternative $content_object_alternative
     * @param $base_content_object_id
     * @param $metadata_element_id
     * @return bool
     */
    public static function update_content_object_alternative_to_new_metadata_element(
        ContentObjectAlternative $content_object_alternative, $base_content_object_id, $metadata_element_id)
    {
        if ($metadata_element_id == $content_object_alternative->get_metadata_element_id())
        {
            return true;
        }
        
        if (! $content_object_alternative->delete())
        {
            return false;
        }
        
        if (! self :: create_content_object_alternatives(
            $base_content_object_id, 
            array($content_object_alternative->get_content_object_id()), 
            $metadata_element_id))
        {
            return false;
        }
        
        return true;
    }

    /**
     * Creates a single content object alternative with a content object id, a link number and the metadata_element_id
     * 
     * @param int $content_object_id
     * @param int $link_number
     * @param int $metadata_element_id
     *
     * @return bool
     */
    protected function create_content_object_alternative($content_object_id, $link_number, $metadata_element_id)
    {
        $content_object_alternative = new ContentObjectAlternative();
        
        $content_object_alternative->set_content_object_id($content_object_id);
        $content_object_alternative->set_link_number($link_number);
        $content_object_alternative->set_metadata_element_id($metadata_element_id);
        
        return $content_object_alternative->create();
    }

    /**
     * Retrieves the linked content object id's
     * 
     * @param int $content_object_id
     * @param bool $include_original_content_object
     *
     * @return array
     */
    public static function retrieve_linked_content_object_ids($content_object_id, 
        $include_original_content_object = false)
    {
        $properties = new DataClassProperties();
        
        $properties->add(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID));
        
        $parameters = new RecordRetrievesParameters(
            $properties, 
            self :: get_alternative_content_objects_condition(
                $content_object_id, 
                null, 
                $include_original_content_object));
        
        $records = self :: records(ContentObjectAlternative :: class_name(), $parameters);
        
        $content_object_ids = array();
        
        while ($record = $records->next_result())
        {
            $content_object_ids[] = $record[ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID];
        }
        
        return $content_object_ids;
    }

    /**
     * Retrieves the content objects that are linked to the given content object
     * 
     * @param $content_object_id
     * @param bool $include_original_content_object
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_linked_content_objects($content_object_id, $include_original_content_object = false)
    {
        $joins = new Joins();
        
        $joins->add(
            new Join(
                ContentObjectAlternative :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        ContentObjectAlternative :: class_name(), 
                        ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID))));
        
        $parameters = new DataClassRetrievesParameters(
            self :: get_alternative_content_objects_condition(
                $content_object_id, 
                null, 
                $include_original_content_object), 
            null, 
            null, 
            array(), 
            $joins);
        
        return \Chamilo\Core\Repository\Storage\DataManager :: retrieves(ContentObject :: class_name(), $parameters);
    }

    /**
     * Returns the joins for the content_object_alternative data class
     * 
     * @return Joins
     */
    protected static function get_alternative_content_object_joins()
    {
        $joins = new Joins();
        
        $joins->add(
            new Join(
                ContentObject :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        ContentObjectAlternative :: class_name(), 
                        ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID))));
        
        $joins->add(
            new Join(
                Element :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                    new PropertyConditionVariable(
                        ContentObjectAlternative :: class_name(), 
                        ContentObjectAlternative :: PROPERTY_METADATA_ELEMENT_ID))));
        
        $joins->add(
            new Join(
                ContentObjectMetadataElementValue :: class_name(), 
                new AndCondition(
                    array(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                ContentObjectMetadataElementValue :: class_name(), 
                                ContentObjectMetadataElementValue :: PROPERTY_CONTENT_OBJECT_ID), 
                            new PropertyConditionVariable(
                                ContentObjectAlternative :: class_name(), 
                                ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID)), 
                        new EqualityCondition(
                            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_ID), 
                            new PropertyConditionVariable(
                                ContentObjectMetadataElementValue :: class_name(), 
                                ContentObjectMetadataElementValue :: PROPERTY_ELEMENT_ID)))), 
                Join :: TYPE_LEFT));
        
        return $joins;
    }

    /**
     * Returns the condition for the content object alternative data class
     * 
     * @param int $content_object_id
     * @param \libraries\storage\Condition $condition
     * @param bool $include_original_content_object
     *
     * @return \libraries\storage\InCondition
     */
    protected static function get_alternative_content_objects_condition($content_object_id, $condition = null, 
        $include_original_content_object = false)
    {
        $conditions = array();
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectAlternative :: class_name(), 
                ContentObjectAlternative :: PROPERTY_LINK_NUMBER), 
            self :: retrieve_link_numbers_for_content_object($content_object_id));
        
        if (! $include_original_content_object)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectAlternative :: class_name(), 
                        ContentObjectAlternative :: PROPERTY_CONTENT_OBJECT_ID), 
                    new StaticConditionVariable($content_object_id)));
        }
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        return new AndCondition($conditions);
    }
}