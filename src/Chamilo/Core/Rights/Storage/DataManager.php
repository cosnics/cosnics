<?php
namespace Chamilo\Core\Rights\Storage;

use Chamilo\Core\Rights\RightsLocationEntityRight;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'rights_';

    // TODO: Fix DataManager implementation (retrieve_granted_rights_array)
    public static function retrieve_identifiers_with_right_granted($right_id, $context, $entities_condition, $condition,
        $parent_has_right, $offset = null, $max_objects = null)
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        if (! $parent_has_right)
        {

            $join_condition = new EqualityCondition(
                new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_ID),
                new PropertyConditionVariable(
                    $context_entity_right :: class_name(),
                    $context_entity_right :: PROPERTY_LOCATION_ID));
            if ($right_id)
            {
                $right_id_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        $context_entity_right :: class_name(),
                        $context_entity_right :: PROPERTY_RIGHT_ID),
                    new StaticConditionVariable($right_id));
                $join_condition = new AndCondition($join_condition, $right_id_condition);
            }

            $join = new Join($context_entity_right :: class_name(), $join_condition);

            $joins = new Joins();
            $joins->add($join);

            if ($entities_condition)
            {
                $condition = new AndCondition($condition, $entities_condition);
            }

            $parameters = new DataClassDistinctParameters($condition, $context_location :: PROPERTY_IDENTIFIER, $joins);

            return $context_dm :: distinct($context_location :: class_name(), $parameters);
        }
        else
        {
            // Inheriting children
            $inheriting_condition = new AndCondition(
                $condition,
                new EqualityCondition(
                    new PropertyConditionVariable(
                        $context_location :: class_name(),
                        $context_location :: PROPERTY_INHERIT),
                    new StaticConditionVariable(1)));
            $parameters = new DataClassDistinctParameters(
                $inheriting_condition,
                $context_location :: PROPERTY_IDENTIFIER);
            $inheriting_identifiers = $context_dm :: distinct($context_location :: class_name(), $parameters);

            // Non-inheriting children
            $join = new Join(
                $context_entity_right :: class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_ID),
                    new PropertyConditionVariable(
                        $context_entity_right :: class_name(),
                        $context_entity_right :: PROPERTY_LOCATION_ID)));

            $joins = new Joins(array($join));

            $non_inheriting_conditions = array();
            $non_inheriting_conditions[] = $condition;
            $non_inheriting_conditions[] = $entities_condition;
            $non_inheriting_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_INHERIT),
                new StaticConditionVariable(0));
            $non_inheriting_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_entity_right :: class_name(),
                    $context_entity_right :: PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id));

            $parameters = new DataClassDistinctParameters(
                new AndCondition($non_inheriting_conditions),
                $context_location :: PROPERTY_IDENTIFIER,
                $joins);

            $non_inheriting_identifiers = $context_dm :: distinct($context_location :: class_name(), $parameters);

            return array_merge($inheriting_identifiers, $non_inheriting_identifiers);
        }
    }

    public static function retrieve_location_overview_with_rights_granted($context, $condition, $entities_condition,
        $offset = null, $max_objects = null)
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $properties = new DataClassProperties();

        $properties->add(
            new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_TYPE));
        $properties->add(
            new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_IDENTIFIER));

        $join_conditions = array();

        if ($entities_condition)
        {
            $join_conditions[] = $entities_condition;
        }

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $context_entity_right :: class_name(),
                $context_entity_right :: PROPERTY_LOCATION_ID),
            new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_ID));

        $join = new Join($context_entity_right :: class_name(), new AndCondition($join_conditions));

        $joins = new Joins(array($join));

        $parameters = new RecordRetrievesParameters($properties, $condition, $max_objects, $offset, null, $joins);

        return $context_dm :: records($context_location :: class_name(), $parameters);
    }

    public static function count_location_overview_with_rights_granted($context, $condition, $entities_condition)
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $join_conditions = array();

        if ($entities_condition)
        {
            $join_conditions[] = $entities_condition;
        }

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $context_entity_right :: class_name(),
                $context_entity_right :: PROPERTY_LOCATION_ID),
            new PropertyConditionVariable($context_location :: class_name(), $context_location :: PROPERTY_ID));

        $join = new Join($context_entity_right :: class_name(), new AndCondition(array($join_conditions)));

        $joins = new Joins(array($join));

        $parameters = new DataClassCountDistinctParameters($condition, $context_location :: PROPERTY_IDENTIFIER, $joins);
        return $context_dm :: count_distinct($context_location :: class_name(), $parameters);
    }

    /*
     * @deprecated Provided for backwards campatibility towards callers that use methods parametrized with a $context
     * This could be easily replaced with a generic retrieve, as is evidenced from the method body.
     */
    public function retrieve_rights_location($context, $condition = null)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        $rights_location = $context_dm :: retrieve($context_class :: class_name(), $condition);

        if ($rights_location)
        {
            $rights_location->set_context($context);
        }
        return $rights_location;
    }

    public static function retrieve_rights_location_by_identifier($context, $type, $identifier, $tree_identifier = '0',
        $tree_type = RightsUtil :: TREE_TYPE_ROOT)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_TREE_TYPE),
            new StaticConditionVariable($tree_type));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($tree_identifier));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_IDENTIFIER),
            new StaticConditionVariable($identifier));

        if ($type != null)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_TYPE),
                new StaticConditionVariable($type));
        }

        $condition = new AndCondition($conditions);

        $location = $context_dm :: retrieve($context_class :: class_name(), $condition);

        if (! $location)
        {
            return false;
        }

        return $location;
    }

    /*
     * @deprecated Provided for backwards campatibility towards callers that use methods parametrized with a $context
     * This could be easily replaced with a generic retrieve_by_id, as is evidenced from the method body.
     */
    public static function retrieve_rights_location_by_id($context, $location_id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        $location = $context_dm :: retrieve_by_id($context_class :: class_name(), $location_id);

        if (! $location)
        {
            return null;
        }

        return $location;
    }

    public static function retrieve_rights_location_entity_right($context, $right, $entity_id, $entity_type,
        $location_id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entity_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity_type));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_RIGHT_ID),
            new StaticConditionVariable($right));
        $condition = new AndCondition($conditions);

        $rights_location_entity_right = $context_dm :: retrieve($context_class :: class_name(), $condition);

        if ($rights_location_entity_right)
        {
            $rights_location_entity_right->set_context($context);
        }
        return $rights_location_entity_right;
    }

    /**
     * Removes the entity rights linked to a location.
     */
    public static function delete_rights_location_entity_rights($location, $entity_type = null, $entity_id = null,
        $right_id = null)
    {
        $context = $location->get_context();
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $condition = new EqualityCondition(
            new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location->get_id()));

        $additional_conditions = array();
        if ($entity_type != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable($entity_type));
        }
        if ($entity_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_ENTITY_ID),
                new StaticConditionVariable($entity_id));
        }
        if ($right_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class :: class_name(), $context_class :: PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id));
        }

        if (count($additional_conditions) > 0)
        {
            $additional_conditions[] = $condition;
            $condition = new AndCondition($additional_conditions);
        }

        return $context_dm :: deletes($context_class :: class_name(), $condition);
    }

    /*
     * @deprecated Provided for backwards campatibility for the migration package which post_processes locations from
     * different contexts. This could be easily replaced with a generic retrieves, as is evidenced from the method body.
     */
    public static function retrieve_rights_locations($context, $condition = null, $offset = null, $max_objects = null,
        $order_by = null)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        return $context_dm :: retrieves(
            $context_class :: class_name(),
            new DataClassRetrievesParameters($condition, $max_objects, $offset, $order_by));
    }

    /*
     * @deprecated Provided for backwards campatibility towards callers that use methods parametrized with a $context
     * This could be easily replaced with a generic retrieves, as is evidenced from the method body.
     */
    public static function retrieve_rights_location_rights($context, $condition = null, $offset = null, $max_objects = null,
        $order_by = null)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        return $context_dm :: retrieves(
            $context_class :: class_name(),
            new DataClassRetrievesParameters($condition, $max_objects, $offset, $order_by));
    }

    /*
     * @deprecated Provided for backwards campatibility for the migration package which post_processes locations from
     * different contexts. This could be easily replaced with a generic retrieve_by_id, as is evidenced from the method
     * body.
     */
    public static function retrieve_rights_location_entity_right_by_id($context, $id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $rights_location_entity_right = $context_dm :: retrieve_by_id($context_class :: class_name(), $id);

        if ($rights_location_entity_right)
        {
            $rights_location_entity_right->set_context($context);
        }
        return $rights_location_entity_right;
    }

    public static function retrieve_granted_rights_array($location, $entities_condition)
    {
        $context = $location->get_context();
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $properties = new DataClassProperties();
        $properties->add(
            new PropertyConditionVariable(
                $context_entity_right :: class_name(),
                $context_entity_right :: PROPERTY_RIGHT_ID));

        $join = new Join(
            $context_entity_right,
            new AndCondition(
                array(
                    new EqualityCondition(
                        new PropertyConditionVariable($context_location, $context_location :: PROPERTY_ID),
                        new StaticConditionVariable($location->get_id())),

                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $context_entity_right,
                            $context_entity_right :: PROPERTY_LOCATION_ID),
                        new PropertyConditionVariable(
                            $context_location :: class_name(),
                            $context_location :: PROPERTY_ID)))));

        $joins = new Joins(array($join));

        $parameters = new RecordRetrievesParameters($properties, $entities_condition, null, null, array(), $joins);
        $result_set = $context_dm :: records($context_location :: class_name(), $parameters);

        $granted_rights = array();

        while ($result = $result_set->next_result())
        {
            $granted_rights[] = $result[RightsLocationEntityRight :: PROPERTY_RIGHT_ID];
        }

        if ($location->inherits())
        {
            $parent_location = self :: retrieve_rights_location_by_id(
                $location->get_context(),
                $location->get_parent_id());
            if ($parent_location)
            {
                $parent_rights = self :: retrieve_granted_rights_array($parent_location, $entities_condition);
                $granted_rights = array_merge($granted_rights, $parent_rights);
            }
        }

        return $granted_rights;
    }

    /**
     * Retrieves the entities (type and id) that have the given right granted
     *
     * @param $context <type>
     * @param $right_id <type>
     * @param $location <type>
     * @return <array> two dimensional array: type => id
     */
    public function retrieve_target_entities_array($right_id, $context, $location)
    {
        if (is_null($location) && ! is_object($location))
        {
            return array();
        }

        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $properties = new DataClassProperties();
        $properties->add(
            new PropertyConditionVariable(
                $context_entity_right :: class_name(),
                $context_entity_right :: PROPERTY_ENTITY_TYPE));
        $properties->add(
            new PropertyConditionVariable(
                $context_entity_right :: class_name(),
                $context_entity_right :: PROPERTY_ENTITY_ID));

        $join = new Join(
            $context_entity_right :: class_name(),
            new AndCondition(
                array(
                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $context_entity_right :: class_name(),
                            $context_entity_right :: PROPERTY_LOCATION_ID),
                        new PropertyConditionVariable(
                            $context_location :: class_name(),
                            $context_location :: PROPERTY_ID)))));

        $joins = new Joins(array($join));

        $condition = new EqualityCondition(
            new PropertyConditionVariable($context_location, $context_location :: PROPERTY_ID),
            new StaticConditionVariable($location->get_id()));

        if (! is_null($right_id))
        {
            $conditions[] = $condition;

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_entity_right, $context_entity_right :: PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id));

            $condition = new AndCondition($conditions);
        }

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, array(), $joins);
        $result_set = $context_dm :: records($context_location :: class_name(), $parameters);

        $target_entities = array();
        while ($result = $result_set->next_result())
        {
            $target_entities[$result[$context_entity_right :: PROPERTY_ENTITY_TYPE]][] = $result[$context_entity_right :: PROPERTY_ENTITY_ID];
        }

        if ($location->inherits())
        {
            $parent_location = self :: retrieve_rights_location_by_id(
                $location->get_context(),
                $location->get_parent_id());
            $parent_entities = self :: retrieve_target_entities_array($right_id, $context, $parent_location);
            foreach ($parent_entities as $type => $id_array)
            {
                if ($target_entities[$type])
                {
                    $target_entities[$type] = array_merge($parent_entities[$type], $target_entities[$type]);
                }
                else
                {
                    $target_entities[$type] = $parent_entities[$type];
                }
            }
        }
        return $target_entities;
    }

    public static function retrieve_rights_location_rights_for_location($context, $location_id, $rights)
    {
        $class_name = $context . '\Storage\DataClass\\' . RightsLocationEntityRight :: class_name(false);

        if (! is_array($rights))
        {
            $rights = array($rights);
        }
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id));
        $conditions[] = new InCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_RIGHT_ID),
            $rights);

        $condition = new AndCondition($conditions);

        // order by entity_type to avoid invalid data when looping the rights
        $order = new OrderBy(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight :: PROPERTY_ENTITY_TYPE),
            SORT_ASC);

        return self :: retrieve_rights_location_rights($context, $condition, null, null, $order);
    }
}
