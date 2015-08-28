<?php
namespace Chamilo\Application\Portfolio\Storage;

use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Portfolio DataManager
 * 
 * @package application\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'portfolio_';

    /**
     * Retrieve all users for which a portfolio is published
     * 
     * @param \libraries\storage\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \libraries\ObjectTableOrder[] $order_property
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_users($condition, $count, $offset, $order_property)
    {
        $user_parameters = new DataClassDistinctParameters(null, Publication :: PROPERTY_PUBLISHER_ID);
        $user_ids = DataManager :: distinct(Publication :: class_name(), $user_parameters);
        
        if ($condition)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = new InCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), 
                $user_ids);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new InCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), 
                $user_ids);
        }
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        
        return \Chamilo\Core\User\Storage\DataManager :: retrieves(User :: class_name(), $parameters);
    }

    /**
     * Count the number of users for which a portfolio is published
     * 
     * @param \libraries\storage\Condition $condition
     * @return int
     */
    public static function count_users($condition)
    {
        $user_parameters = new DataClassDistinctParameters(null, Publication :: PROPERTY_PUBLISHER_ID);
        $user_ids = DataManager :: distinct(Publication :: class_name(), $user_parameters);
        
        if ($condition)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = new InCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), 
                $user_ids);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new InCondition(
                new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), 
                $user_ids);
        }
        
        $parameters = new DataClassCountParameters($condition);
        
        return \Chamilo\Core\User\Storage\DataManager :: count(User :: class_name(), $parameters);
    }

    /**
     * Retrieve all RightsLocationEntityRights instances for a given publication id, location id and a set of rights
     * 
     * @param int $publication_id
     * @param string $location_id
     * @param int[] $rights
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_rights_location_rights_for_location($publication_id, $location_id, $rights)
    {
        if (! is_array($rights))
        {
            $rights = array($rights);
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_LOCATION_ID), 
            new StaticConditionVariable($location_id));
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_RIGHT_ID), 
            $rights);
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($publication_id));
        $condition = new AndCondition($conditions);
        
        $order = new OrderBy(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_ENTITY_TYPE), 
            SORT_ASC);
        
        return self :: retrieves(
            RightsLocationEntityRight :: class_name(), 
            new DataClassRetrievesParameters($condition, null, null, $order));
    }

    /**
     * Retrieve all RightsLocationEntityRights instances for a given right, entity id, entity type, location id and
     * publication id
     * 
     * @param int $right
     * @param int $entity_id
     * @param int $entity_type
     * @param string $location_id
     * @param int $publication_id
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_rights_location_entity_right($right, $entity_id, $entity_type, $location_id, 
        $publication_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_ENTITY_ID), 
            new StaticConditionVariable($entity_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable($entity_type));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_LOCATION_ID), 
            new StaticConditionVariable($location_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_RIGHT_ID), 
            new StaticConditionVariable($right));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_PUBLICATION_ID), 
            new StaticConditionVariable($publication_id));
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(RightsLocationEntityRight :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Removes the entity rights linked to a location (and optionally an entity type and entity id)
     * 
     * @param RightsLocation $location
     * @param int $entity_type
     * @param int $entity_id
     * @param int $right_id
     */
    public static function delete_rights_location_entity_rights($location, $entity_type = null, $entity_id = null, 
        $right_id = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight :: class_name(), 
                RightsLocationEntityRight :: PROPERTY_LOCATION_ID), 
            new StaticConditionVariable($location->get_node_id()));
        
        $additional_conditions = array();
        if ($entity_type != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight :: class_name(), 
                    RightsLocationEntityRight :: PROPERTY_ENTITY_TYPE), 
                new StaticConditionVariable($entity_type));
        }
        if ($entity_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight :: class_name(), 
                    RightsLocationEntityRight :: PROPERTY_ENTITY_ID), 
                new StaticConditionVariable($entity_id));
        }
        if ($right_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight :: class_name(), 
                    RightsLocationEntityRight :: PROPERTY_RIGHT_ID), 
                new StaticConditionVariable($right_id));
        }
        
        if (count($additional_conditions) > 0)
        {
            $additional_conditions[] = $condition;
            $condition = new AndCondition($additional_conditions);
        }
        
        return self :: deletes(RightsLocationEntityRight :: class_name(), $condition);
    }
}
