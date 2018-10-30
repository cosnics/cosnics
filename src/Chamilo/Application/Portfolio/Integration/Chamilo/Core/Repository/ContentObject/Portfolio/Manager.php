<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\ContentObject\Portfolio;

use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocation;
use Chamilo\Application\Portfolio\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Portfolio\PortfolioInterface;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperty;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
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
     * Update references to a given set of node ids
     *
     * @param int[] $old_node_ids
     * @param int[] $new_node_ids
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
            $properties = new DataClassProperties();
            $properties->add(
                new DataClassProperty(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class_name(),
                        RightsLocationEntityRight::PROPERTY_LOCATION_ID),
                    new StaticConditionVariable($new_node_id)));

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    RightsLocationEntityRight::class_name(),
                    RightsLocationEntityRight::PROPERTY_LOCATION_ID),
                new StaticConditionVariable($old_node_id));

            if (! DataManager::updates(RightsLocationEntityRight::class_name(), $properties, $condition))
            {
                return false;
            }

            $properties = new DataClassProperties();
            $properties->add(
                new DataClassProperty(
                    new PropertyConditionVariable(RightsLocation::class_name(), RightsLocation::PROPERTY_NODE_ID),
                    new StaticConditionVariable($new_node_id)));

            $condition = new EqualityCondition(
                new PropertyConditionVariable(RightsLocation::class_name(), RightsLocation::PROPERTY_NODE_ID),
                new StaticConditionVariable($old_node_id));

            if (! DataManager::updates(RightsLocation::class_name(), $properties, $condition))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete references to the given node ids
     *
     * @param int[] $node_ids
     * @return boolean
     */
    public static function delete_node_ids($node_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                RightsLocationEntityRight::class_name(),
                RightsLocationEntityRight::PROPERTY_LOCATION_ID),
            $node_ids);

        if (! DataManager::deletes(RightsLocationEntityRight::class_name(), $condition))
        {
            return false;
        }

        $condition = new InCondition(
            new PropertyConditionVariable(RightsLocation::class_name(), RightsLocation::PROPERTY_NODE_ID),
            $node_ids);

        if (! DataManager::deletes(RightsLocation::class_name(), $condition))
        {
            return false;
        }

        return true;
    }
}
