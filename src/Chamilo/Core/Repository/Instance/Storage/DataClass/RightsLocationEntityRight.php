<?php
namespace Chamilo\Core\Repository\Instance\Storage\DataClass;

use Chamilo\Core\Repository\Instance\Storage\DataManager;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_instance_rights_location_entity_right';
    }
}
