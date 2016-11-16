<?php
namespace Chamilo\Core\Admin\Announcement\Storage;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Rights;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'admin_announcement_';

    /**
     * Counts publications
     * 
     * @param \libraries\storage\Condition $condition
     * @return int
     */
    public static function count_publications_for_me($condition, $user_id = null)
    {
        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = UserEntity::getInstance();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = PlatformGroupEntity::getInstance();
        
        $condition = self::get_publications_with_view_right_granted_location_condition($entities, $condition, $user_id);
        
        $parameters = new DataClassCountParameters($condition, self::get_publication_joins());
        return self::count(Publication::class_name(), $parameters);
    }

    /**
     * Counts publications
     * 
     * @param \libraries\storage\Condition $condition
     * @return int
     */
    public static function count_publications($condition)
    {
        $parameters = new DataClassCountParameters($condition, self::get_publication_joins());
        
        return self::count(Publication::class_name(), $parameters);
    }

    /**
     * Retrieves publications joined with the repository content object table
     * 
     * @param \libraries\storage\Condition $condition
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param int $offset
     * @param int $max_objects
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_publications_for_me($condition = null, $order_by = array(), $offset = 0, $max_objects = -1, 
        $user_id = null)
    {
        $entities = array();
        $entities[UserEntity::ENTITY_TYPE] = UserEntity::getInstance();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = PlatformGroupEntity::getInstance();
        
        $condition = self::get_publications_with_view_right_granted_location_condition($entities, $condition, $user_id);
        
        return self::retrieve_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * Retrieves publications joined with the repository content object table
     * 
     * @param \libraries\storage\Condition $condition
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param int $offset
     * @param int $max_objects
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_publications($condition = null, $order_by = array(), $offset = 0, $max_objects = -1)
    {
        $data_class_properties = array();
        
        $data_class_properties[] = new PropertiesConditionVariable(Publication::class_name());
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_TITLE);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_DESCRIPTION);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_TYPE);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_CURRENT);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_OWNER_ID);
        
        $properties = new DataClassProperties($data_class_properties);
        
        $parameters = new RecordRetrievesParameters(
            $properties, 
            $condition, 
            $max_objects, 
            $offset, 
            $order_by, 
            self::get_publication_joins());
        
        return self::records(Publication::class_name(), $parameters);
    }

    /**
     * Retrieves the conditions for the publications with view right granted
     * 
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param Condition $condition
     * @param int $user_id
     * @return Condition
     */
    protected static function get_publications_with_view_right_granted_location_condition($entities, $condition, 
        $user_id = null)
    {
        $conditions = self::get_publication_conditions_with_right(Rights::VIEW_RIGHT, $entities, $user_id, $condition);
        
        if (is_array($conditions) && count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }

    /**
     * Returns the conditions for the publications for a given right
     * 
     * @param int $right
     * @param RightsLocation $parent_location
     * @param RightsEntity[] $entities
     * @param int $user_id
     * @param Condition $condition
     * @return Condition
     */
    protected static function get_publication_conditions_with_right($right, $entities, $user_id, $condition)
    {
        $conditions = array();
        
        $granted_location_ids = Rights::getInstance()->get_identifiers_with_right_granted(
            $right, 
            Manager::context(), 
            Rights::getInstance()->get_root(Manager::context()), 
            Rights::TYPE_PUBLICATION, 
            $user_id, 
            $entities);
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID), 
            $granted_location_ids);
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        return $conditions;
    }

    /**
     * Returns the joins for the publication with the content object table
     * 
     * @return \libraries\storage\Joins
     */
    protected static function get_publication_joins()
    {
        $joins = array();
        
        $joins[] = new Join(
            ContentObject::class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID)));
        
        return new Joins($joins);
    }

    public static function get_content_object_publication_attributes($object_id, $type = null, $condition = null, $count = null, 
        $offset = null, $order_properties = null)
    {
        $order_by = array();
        
        if (isset($type))
        {
            if ($type == 'user')
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID), 
                    new StaticConditionVariable(Session::get_user_id()));
                
                foreach ($order_properties as $order_property)
                {
                    $property = $order_property->get_property();
                    
                    if ($property != 'application' && $property != 'location')
                    {
                        $order_by[] = $order_property;
                    }
                }
            }
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
                new StaticConditionVariable($object_id));
        }
        
        $result = self::retrieve_publications($condition, $order_by, $offset, $count);
        
        $publication_attributes = array();
        
        while ($record = $result->next_result())
        {
            $publication_attributes[] = self::create_publication_attributes_from_record($record);
        }
        
        return $publication_attributes;
    }

    public static function get_content_object_publication_attribute($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID), 
            new StaticConditionVariable($publication_id));
        
        $record = self::record(
            Publication::class_name(), 
            new RecordRetrieveParameters(
                new DataClassProperties(new PropertiesConditionVariable(Publication::class_name())), 
                $condition));
        
        return self::create_publication_attributes_from_record($record);
    }

    /**
     * Checks if a content object is published
     * 
     * @param int $object_id
     *
     * @return bool
     */
    public static function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($object_id));
        return self::count(Publication::class_name(), $condition) >= 1;
    }

    public static function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
            $object_ids);
        return self::count(Publication::class_name(), $condition) >= 1;
    }

    public static function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        $conditions = array();
        
        if (! $object_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID), 
                new StaticConditionVariable($user->get_id()));
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
                new StaticConditionVariable($object_id));
        }
        
        if ($condition)
        {
            $conditions[] = $condition;
        }
        
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassCountParameters($condition, self::get_publication_joins());
        return self::count(Publication::class_name(), $parameters);
    }

    public static function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($object_id));
        $publications = self::retrieves(Publication::class_name(), new DataClassRetrievesParameters($condition));
        
        while ($publication = $publications->next_result())
        {
            if (! $publication->delete())
            {
                return false;
            }
        }
        
        return true;
    }

    public static function delete_content_object_publication($publication_id)
    {
        $publication = self::retrieve_by_id(Publication::class_name(), (int) $publication_id);
        
        if (! $publication instanceof Publication || ! $publication->delete())
        {
            return false;
        }
        
        return true;
    }

    /**
     * Creates a publication attributes object from a given record
     * 
     * @param $record
     * @return \core\repository\publication\storage\data_class\Attributes
     */
    protected static function create_publication_attributes_from_record($record)
    {
        $attributes = new \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes();
        
        $attributes->set_id($record[Publication::PROPERTY_ID]);
        $attributes->set_publisher_user_id($record[Publication::PROPERTY_PUBLISHER_ID]);
        $attributes->set_publication_date($record[Publication::PROPERTY_PUBLICATION_DATE]);
        $attributes->set_application(__NAMESPACE__);
        
        $url = 'index.php?application=admin&amp;go=' . \Chamilo\Core\Admin\Manager::ACTION_SYSTEM_ANNOUNCEMENTS .
             '&amp;announcement_action=' . Manager::ACTION_VIEW . '&amp;' . Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID . '=' .
             $attributes->get_id();
        
        $attributes->set_url($url);
        $attributes->set_publication_object_id($record[Publication::PROPERTY_CONTENT_OBJECT_ID]);
        
        return $attributes;
    }

    /**
     * Retrieves the publications for a given user id
     * 
     * @param $user_id
     * @return \common\libraries\ResultSet
     */
    public static function retrieve_publications_for_user($user_id)
    {
        $from_date_variables = new PropertyConditionVariable(
            Publication::class_name(), 
            Publication::PROPERTY_FROM_DATE);
        
        $to_date_variable = new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_TO_DATE);
        
        $time_conditions = array();
        
        $time_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_HIDDEN), 
            new StaticConditionVariable(0));
        
        $forever_conditions = array();
        $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));
        $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));
        $forever_condition = new AndCondition($forever_conditions);
        
        $between_conditions = array();
        
        $between_conditions[] = new InequalityCondition(
            $from_date_variables, 
            InequalityCondition::LESS_THAN_OR_EQUAL, 
            new StaticConditionVariable(time()));
        $between_conditions[] = new InequalityCondition(
            $to_date_variable, 
            InequalityCondition::GREATER_THAN_OR_EQUAL, 
            new StaticConditionVariable(time()));
        
        $between_condition = new AndCondition($between_conditions);
        
        $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));
        
        $condition = new AndCondition($time_conditions);
        
        return self::retrieve_publications_for_me(
            $condition, 
            array(
                new OrderBy(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_MODIFICATION_DATE), 
                    SORT_DESC)), 
            0, 
            - 1, 
            $user_id);
    }
}
