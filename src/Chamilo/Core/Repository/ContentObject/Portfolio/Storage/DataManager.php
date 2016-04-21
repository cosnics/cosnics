<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Storage;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;

/**
 * Portfolio datamanager
 *
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    /**
     * Update references to a given set of node ids
     *
     * @param int[] $old_node_ids
     * @param int[] $new_node_ids
     * @return boolean
     */
    public static function update_node_ids($old_node_ids, $new_node_ids)
    {
        $registrations = Configuration :: get_instance()->getIntegrationRegistrations(Portfolio :: package());

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration :: PROPERTY_CONTEXT] . '\Manager';

            if (class_exists($manager_class))
            {
                $result = $manager_class :: update_node_ids($old_node_ids, $new_node_ids);

                if (! $result)
                {
                    return false;
                }
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
        $registrations = Configuration :: get_instance()->getIntegrationRegistrations(Portfolio :: package());

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration :: PROPERTY_CONTEXT] . '\Manager';

            if (class_exists($manager_class))
            {
                $result = $manager_class :: delete_node_ids($node_ids);

                if (! $result)
                {
                    return false;
                }
            }
        }

        return true;
    }
}
