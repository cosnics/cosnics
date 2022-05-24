<?php
namespace Chamilo\Application\Portfolio\Storage;

use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Portfolio\Storage
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'portfolio_';

    /**
     * Removes the entity rights linked to a location (and optionally an entity type and entity id)
     *
     * @param RightsLocation $location
     * @param int $entity_type
     * @param int $entity_id
     * @param int $right_id
     */
    public static function delete_rights_location_entity_rights(
        $location, $entity_type = null, $entity_id = null, $right_id = null
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($location->get_node_id())
        );

        $additional_conditions = [];
        if ($entity_type != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
                ), new StaticConditionVariable($entity_type)
            );
        }
        if ($entity_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_ID
                ), new StaticConditionVariable($entity_id)
            );
        }
        if ($right_id != null)
        {
            $additional_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_RIGHT_ID
                ), new StaticConditionVariable($right_id)
            );
        }

        if (count($additional_conditions) > 0)
        {
            $additional_conditions[] = $condition;
            $condition = new AndCondition($additional_conditions);
        }

        return self::deletes(RightsLocationEntityRight::class, $condition);
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
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public static function retrieve_rights_location_entity_right(
        $right, $entity_id, $entity_type, $location_id, $publication_id
    )
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_ID
            ), new StaticConditionVariable($entity_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable($entity_type)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($location_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), new StaticConditionVariable($right)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_PUBLICATION_ID
            ), new StaticConditionVariable($publication_id)
        );
        $condition = new AndCondition($conditions);

        return self::retrieve(RightsLocationEntityRight::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieve all RightsLocationEntityRights instances for a given publication id, location id and a set of rights
     *
     * @param int $publication_id
     * @param string $location_id
     * @param int[] $rights
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public static function retrieve_rights_location_rights_for_location($publication_id, $location_id, $rights)
    {
        if (!is_array($rights))
        {
            $rights = array($rights);
        }

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), new StaticConditionVariable($location_id)
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_RIGHT_ID
            ), $rights
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_PUBLICATION_ID
            ), new StaticConditionVariable($publication_id)
        );
        $condition = new AndCondition($conditions);

        $order = new OrderProperty(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ENTITY_TYPE
            ), SORT_ASC
        );

        return self::retrieves(
            RightsLocationEntityRight::class,
            new DataClassRetrievesParameters($condition, null, null, new OrderBy(array($order)))
        );
    }
}
