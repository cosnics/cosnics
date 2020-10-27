<?php
namespace Chamilo\Core\Repository\Instance\Storage\DataClass;

use Chamilo\Core\Repository\Instance\Storage\DataManager;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     *
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_instance_rights_location';
    }
}
