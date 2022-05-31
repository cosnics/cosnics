<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\ContentObject\Portfolio;

use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation;
use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Portfolio\PortfolioInterface;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\UpdateProperties;
use Chamilo\Libraries\Storage\Query\UpdateProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\ContentObject\Portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Manager implements PortfolioInterface
{

    /**
     * Delete references to the given node ids
     *
     * @param int[] $node_ids
     *
     * @return boolean
     */
    public static function delete_node_ids($node_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_LOCATION_ID
            ), $node_ids
        );

        if (!DataManager::deletes(RightsLocationEntityRight::class, $condition))
        {
            return false;
        }

        $condition = new InCondition(
            new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_NODE_ID), $node_ids
        );

        if (!DataManager::deletes(RightsLocation::class, $condition))
        {
            return false;
        }

        return true;
    }

    /**
     * Update references to a given set of node ids
     *
     * @param int[] $old_node_ids
     * @param int[] $new_node_ids
     *
     * @return boolean
     */
    public static function update_node_ids($old_node_ids, $new_node_ids)
    {
        if (count($old_node_ids) != count($new_node_ids))
        {
            return false;
        }

        $node_id_map = array_combine($old_node_ids, $new_node_ids);

        foreach ($node_id_map as $old_node_id => $new_node_id)
        {
            $properties = new UpdateProperties();
            $properties->add(
                new UpdateProperty(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_LOCATION_ID
                    ), new StaticConditionVariable($new_node_id)
                )
            );

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_LOCATION_ID
                ), new StaticConditionVariable($old_node_id)
            );

            if (!DataManager::updates(RightsLocationEntityRight::class, $properties, $condition))
            {
                return false;
            }

            $properties = new UpdateProperties();
            $properties->add(
                new UpdateProperty(
                    new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_NODE_ID),
                    new StaticConditionVariable($new_node_id)
                )
            );

            $condition = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class, RightsLocation::PROPERTY_NODE_ID),
                new StaticConditionVariable($old_node_id)
            );

            if (!DataManager::updates(RightsLocation::class, $properties, $condition))
            {
                return false;
            }
        }

        return true;
    }
}
