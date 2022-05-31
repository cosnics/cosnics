<?php
namespace Chamilo\Core\Rights\Storage;

use Chamilo\Core\Rights\RightsLocationEntityRight;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Rights\Storage
 *
 * @deprecated Use the \Chamilo\Libraries\Rights\Service\RightsService now
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'rights_';

    // TODO: Fix DataManager implementation (retrieve_granted_rights_array)
    // DONE

    public static function count_location_overview_with_rights_granted($context, $condition, $entities_condition)
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $join_conditions = [];

        if ($entities_condition)
        {
            $join_conditions[] = $entities_condition;
        }

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $context_entity_right::class_name(), $context_entity_right::PROPERTY_LOCATION_ID
            ), new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_ID)
        );

        $join = new Join($context_entity_right::class_name(), new AndCondition(array($join_conditions)));

        $joins = new Joins(array($join));

        $parameters = new DataClassCountParameters(
            $condition, $joins, new RetrieveProperties(
                array(
                    new FunctionConditionVariable(
                        FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                            $context_location::class_name(), $context_location::PROPERTY_IDENTIFIER
                        )
                    )
                )
            )
        );

        return $context_dm::count($context_location::class_name(), $parameters);
    }

    // DONE

    public static function delete_rights_location_entity_rights(
        $location, $entity_type = null, $entity_id = null, $right_id = null
    )
    {
        $context = $location->get_context();
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $condition = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location->get_id())
        );

        $additional_conditions = [];
        if ($entity_type != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable($entity_type)
            );
        }
        if ($entity_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_ENTITY_ID),
                new StaticConditionVariable($entity_id)
            );
        }
        if ($right_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id)
            );
        }

        if (count($additional_conditions) > 0)
        {
            $additional_conditions[] = $condition;
            $condition = new AndCondition($additional_conditions);
        }

        return $context_dm::deletes($context_class::class_name(), $condition);
    }

    // DONE

    /**
     * Returns those ID's from $location_ids which user ($entity_condition) has given right to.
     *
     * @return array Keys: Those locations ID's from $location_ids which user has given right to. Values: True.
     */
    public static function filter_location_identifiers_by_granted_right(
        $context, $right, $entity_condition, $location_ids
    )
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');

        $properties = new RetrieveProperties();
        $properties->add(new PropertyConditionVariable($context_class, $context_class::PROPERTY_LOCATION_ID));

        $conditions[] = $entity_condition;
        $conditions[] = new InCondition(
            new PropertyConditionVariable($context_class, $context_class::PROPERTY_LOCATION_ID), $location_ids
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class, $context_class::PROPERTY_RIGHT_ID),
            new StaticConditionVariable($right)
        );
        $condition = new AndCondition($conditions);

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $rights_location_entity_rights = self::records($context_class, $parameters);

        $location_ids = [];

        foreach ($rights_location_entity_rights as $rights_location_entity_right)
        {
            $location_ids[$rights_location_entity_right[$context_class::PROPERTY_LOCATION_ID]] = 1;
        }

        return $location_ids;
    }

    // PERFORMANCE-TWEAKS-START

    // DONE

    public static function retrieve_granted_rights_array($location, $entities_condition)
    {
        $context = $location->get_context();
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $properties = new RetrieveProperties();
        $properties->add(
            new PropertyConditionVariable($context_entity_right::class_name(), $context_entity_right::PROPERTY_RIGHT_ID)
        );

        $join = new Join(
            $context_entity_right, new AndCondition(
                array(
                    new EqualityCondition(
                        new PropertyConditionVariable($context_location, $context_location::PROPERTY_ID),
                        new StaticConditionVariable($location->get_id())
                    ),

                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $context_entity_right, $context_entity_right::PROPERTY_LOCATION_ID
                        ),
                        new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_ID)
                    )
                )
            )
        );

        $joins = new Joins(array($join));

        $parameters = new RecordRetrievesParameters($properties, $entities_condition, null, null, null, $joins);
        $result_set = $context_dm::records($context_location::class_name(), $parameters);

        $granted_rights = [];

        foreach ($result_set as $result)
        {
            $granted_rights[] = $result[RightsLocationEntityRight::PROPERTY_RIGHT_ID];
        }

        if ($location->inherits())
        {
            $parent_location = self::retrieve_rights_location_by_id(
                $location->get_context(), $location->get_parent_id()
            );
            if ($parent_location)
            {
                $parent_rights = self::retrieve_granted_rights_array($parent_location, $entities_condition);
                $granted_rights = array_merge($granted_rights, $parent_rights);
            }
        }

        return $granted_rights;
    }

    // DONE

    public static function retrieve_identifiers_with_right_granted(
        $right_id, $context, $entities_condition, $condition, $parent_has_right, $offset = null, $max_objects = null
    )
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        if (!$parent_has_right)
        {

            $join_condition = new EqualityCondition(
                new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_ID),
                new PropertyConditionVariable(
                    $context_entity_right::class_name(), $context_entity_right::PROPERTY_LOCATION_ID
                )
            );
            if ($right_id)
            {
                $right_id_condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        $context_entity_right::class_name(), $context_entity_right::PROPERTY_RIGHT_ID
                    ), new StaticConditionVariable($right_id)
                );
                $join_condition = new AndCondition([$join_condition, $right_id_condition]);
            }

            $join = new Join($context_entity_right::class_name(), $join_condition);

            $joins = new Joins();
            $joins->add($join);

            if ($entities_condition)
            {
                $condition = new AndCondition([$condition, $entities_condition]);
            }

            $parameters = new DataClassDistinctParameters(
                $condition, new RetrieveProperties(
                array(new PropertyConditionVariable($context_location, $context_location::PROPERTY_IDENTIFIER))
            ), $joins
            );

            return $context_dm::distinct($context_location::class_name(), $parameters);
        }
        else
        {
            // Inheriting children
            $inheriting_condition = new AndCondition([
                $condition,
                new EqualityCondition(
                    new PropertyConditionVariable(
                        $context_location::class_name(), $context_location::PROPERTY_INHERIT
                    ), new StaticConditionVariable(1)
                )
            ]);
            $parameters = new DataClassDistinctParameters(
                $inheriting_condition, new RetrieveProperties(
                    array(new PropertyConditionVariable($context_location, $context_location::PROPERTY_IDENTIFIER))
                )
            );
            $inheriting_identifiers = $context_dm::distinct($context_location::class_name(), $parameters);

            // Non-inheriting children
            $join = new Join(
                $context_entity_right::class_name(), new EqualityCondition(
                    new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_ID),
                    new PropertyConditionVariable(
                        $context_entity_right::class_name(), $context_entity_right::PROPERTY_LOCATION_ID
                    )
                )
            );

            $joins = new Joins(array($join));

            $non_inheriting_conditions = [];
            $non_inheriting_conditions[] = $condition;
            $non_inheriting_conditions[] = $entities_condition;
            $non_inheriting_conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_INHERIT),
                new StaticConditionVariable(0)
            );
            $non_inheriting_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    $context_entity_right::class_name(), $context_entity_right::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right_id)
            );

            $parameters = new DataClassDistinctParameters(
                new AndCondition($non_inheriting_conditions), new RetrieveProperties(
                array(new PropertyConditionVariable($context_location, $context_location::PROPERTY_IDENTIFIER))
            ), $joins
            );

            $non_inheriting_identifiers = $context_dm::distinct($context_location::class_name(), $parameters);

            return array_merge($inheriting_identifiers, $non_inheriting_identifiers);
        }
    }

    //DONE

    public static function retrieve_location_ids_by_identifiers($context, $identifiers, $type)
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');

        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable($context_location, $context_location::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable($context_location, $context_location::PROPERTY_IDENTIFIER));

        $conditions[] = new InCondition(
            new PropertyConditionVariable($context_location, $context_location::PROPERTY_IDENTIFIER), $identifiers
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_location, $context_location::PROPERTY_TYPE),
            new StaticConditionVariable($type)
        );
        $condition = new AndCondition($conditions);

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $locations = self::records($context_location, $parameters);

        $location_ids = [];

        foreach ($locations as $location)
        {
            $location_ids[$location[$context_location::PROPERTY_IDENTIFIER]] =
                $location[$context_location::PROPERTY_ID];
        }

        return $location_ids;
    }

    // PERFORMANCE-TWEAKS-END

    /*
     * @deprecated Provided for backwards campatibility towards callers that use methods parametrized with a $context
     * This could be easily replaced with a generic retrieve, as is evidenced from the method body.
     */
    // DONE

    public static function retrieve_location_overview_with_rights_granted(
        $context, $condition, $entities_condition, $offset = null, $max_objects = null
    )
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $properties = new RetrieveProperties();

        $properties->add(
            new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_TYPE)
        );
        $properties->add(
            new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_IDENTIFIER)
        );

        $join_conditions = [];

        if ($entities_condition)
        {
            $join_conditions[] = $entities_condition;
        }

        $join_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $context_entity_right::class_name(), $context_entity_right::PROPERTY_LOCATION_ID
            ), new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_ID)
        );

        $join = new Join($context_entity_right::class_name(), new AndCondition($join_conditions));

        $joins = new Joins(array($join));

        $parameters = new RecordRetrievesParameters($properties, $condition, $max_objects, $offset, null, $joins);

        return $context_dm::records($context_location::class_name(), $parameters);
    }

    // DONE

    public static function retrieve_location_parent_ids($context, $condition)
    {
        $context_location = ($context . '\Storage\DataClass\RightsLocation');

        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable($context_location, $context_location::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable($context_location, $context_location::PROPERTY_PARENT_ID));

        $parameters = new RecordRetrievesParameters($properties, $condition);

        $locations = self::records($context_location, $parameters);

        $location_parent_ids = [];
        foreach ($locations as $location)
        {
            $location_parent_ids[$location[$context_location::PROPERTY_ID]] =
                $location[$context_location::PROPERTY_PARENT_ID];
        }

        return $location_parent_ids;
    }

    /*
     * @deprecated Provided for backwards campatibility towards callers that use methods parametrized with a $context
     * This could be easily replaced with a generic retrieve_by_id, as is evidenced from the method body.
     */
    // DONE

    public static function retrieve_rights_location($context, $condition = null)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        $rights_location = $context_dm::retrieve(
            $context_class::class_name(), new DataClassRetrieveParameters($condition)
        );

        if ($rights_location)
        {
            $rights_location->set_context($context);
        }

        return $rights_location;
    }

    // DONE

    public static function retrieve_rights_location_by_id($context, $location_id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        $location = $context_dm::retrieve_by_id($context_class::class_name(), $location_id);

        if (!$location)
        {
            return null;
        }

        return $location;
    }

    /**
     * Removes the entity rights linked to a location.
     */

    // DONE
    public static function retrieve_rights_location_by_identifier(
        $context, $type, $identifier, $tree_identifier = '0', $tree_type = RightsUtil::TREE_TYPE_ROOT
    )
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_TREE_TYPE),
            new StaticConditionVariable($tree_type)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_TREE_IDENTIFIER),
            new StaticConditionVariable($tree_identifier)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_IDENTIFIER),
            new StaticConditionVariable($identifier)
        );

        if ($type != null)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_TYPE),
                new StaticConditionVariable($type)
            );
        }

        $condition = new AndCondition($conditions);

        $location = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve(
            $context_class, new DataClassRetrieveParameters($condition)
        );

        if (!$location)
        {
            return false;
        }

        return $location;
    }

    /*
     * @deprecated Provided for backwards campatibility for the migration package which post_processes locations from
     * different contexts. This could be easily replaced with a generic retrieves, as is evidenced from the method body.
     */
    // DONE - No longer used?

    public static function retrieve_rights_location_entity_right(
        $context, $right, $entity_id, $entity_type, $location_id
    )
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entity_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entity_type)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($context_class::class_name(), $context_class::PROPERTY_RIGHT_ID),
            new StaticConditionVariable($right)
        );
        $condition = new AndCondition($conditions);

        $rights_location_entity_right = $context_dm::retrieve(
            $context_class::class_name(), new DataClassRetrieveParameters($condition)
        );

        if ($rights_location_entity_right)
        {
            $rights_location_entity_right->set_context($context);
        }

        return $rights_location_entity_right;
    }

    /*
     * @deprecated Provided for backwards campatibility towards callers that use methods parametrized with a $context
     * This could be easily replaced with a generic retrieves, as is evidenced from the method body.
     */
    // DONE

    public static function retrieve_rights_location_entity_right_by_id($context, $id)
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $rights_location_entity_right = $context_dm::retrieve_by_id($context_class::class_name(), $id);

        if ($rights_location_entity_right)
        {
            $rights_location_entity_right->set_context($context);
        }

        return $rights_location_entity_right;
    }

    /*
     * @deprecated Provided for backwards campatibility for the migration package which post_processes locations from
     * different contexts. This could be easily replaced with a generic retrieve_by_id, as is evidenced from the method
     * body.
     */
    // DONE

    /**
     * @param $context
     * @param null $condition
     * @param null $offset
     * @param null $max_objects
     * @param null $order_by
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     */
    public static function retrieve_rights_location_rights(
        $context, $condition = null, $offset = null, $max_objects = null, $order_by = null
    )
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        return $context_dm::retrieves(
            $context_class::class_name(), new DataClassRetrievesParameters($condition, $max_objects, $offset, $order_by)
        );
    }

    // DONE

    public static function retrieve_rights_location_rights_for_location($context, $location_id, $rights)
    {
        $class_name = $context . '\Storage\DataClass\RightsLocationEntityRight';

        if (!is_array($rights))
        {
            $rights = array($rights);
        }
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            new StaticConditionVariable($location_id)
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_RIGHT_ID), $rights
        );

        $condition = new AndCondition($conditions);

        // order by entity_type to avoid invalid data when looping the rights
        $order = new OrderBy(array(
            new OrderProperty(
                new PropertyConditionVariable($class_name, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE), SORT_ASC
            )
        ));

        return self::retrieve_rights_location_rights($context, $condition, null, null, $order);
    }

    // DONE

    public static function retrieve_rights_locations(
        $context, $condition = null, $offset = null, $max_objects = null, $order_by = null
    )
    {
        $context_class = ($context . '\Storage\DataClass\RightsLocation');
        $context_dm = ($context . '\Storage\DataManager');

        return $context_dm::retrieves(
            $context_class::class_name(), new DataClassRetrievesParameters($condition, $max_objects, $offset, $order_by)
        );
    }

    // DONE

    /**
     * Retrieves the entities (type and id) that have the given right granted
     *
     * @param $context <type>
     * @param $right_id <type>
     * @param $location <type>
     *
     * @return <array> two dimensional array: type => id
     */
    public static function retrieve_target_entities_array($right_id, $context, $location)
    {
        if (is_null($location) && !is_object($location))
        {
            return [];
        }

        $context_location = ($context . '\Storage\DataClass\RightsLocation');
        $context_entity_right = ($context . '\Storage\DataClass\RightsLocationEntityRight');
        $context_dm = ($context . '\Storage\DataManager');

        $properties = new RetrieveProperties();
        $properties->add(
            new PropertyConditionVariable(
                $context_entity_right::class_name(), $context_entity_right::PROPERTY_ENTITY_TYPE
            )
        );
        $properties->add(
            new PropertyConditionVariable(
                $context_entity_right::class_name(), $context_entity_right::PROPERTY_ENTITY_ID
            )
        );

        $join = new Join(
            $context_entity_right::class_name(), new AndCondition(
                array(
                    new EqualityCondition(
                        new PropertyConditionVariable(
                            $context_entity_right::class_name(), $context_entity_right::PROPERTY_LOCATION_ID
                        ),
                        new PropertyConditionVariable($context_location::class_name(), $context_location::PROPERTY_ID)
                    )
                )
            )
        );

        $joins = new Joins(array($join));

        $condition = new EqualityCondition(
            new PropertyConditionVariable($context_location, $context_location::PROPERTY_ID),
            new StaticConditionVariable($location->get_id())
        );

        if (!is_null($right_id))
        {
            $conditions[] = $condition;

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($context_entity_right, $context_entity_right::PROPERTY_RIGHT_ID),
                new StaticConditionVariable($right_id)
            );

            $condition = new AndCondition($conditions);
        }

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, null, $joins);
        $result_set = $context_dm::records($context_location::class_name(), $parameters);

        $target_entities = [];
        foreach ($result_set as $result)
        {
            $target_entities[$result[$context_entity_right::PROPERTY_ENTITY_TYPE]][] =
                $result[$context_entity_right::PROPERTY_ENTITY_ID];
        }

        if ($location->inherits())
        {
            $parent_location = self::retrieve_rights_location_by_id(
                $location->get_context(), $location->get_parent_id()
            );
            $parent_entities = self::retrieve_target_entities_array($right_id, $context, $parent_location);
            foreach ($parent_entities as $type => $id_array)
            {
                if ($target_entities[$type])
                {
                    $target_entities[$type] = array_merge($id_array, $target_entities[$type]);
                }
                else
                {
                    $target_entities[$type] = $id_array;
                }
            }
        }

        return $target_entities;
    }
}
