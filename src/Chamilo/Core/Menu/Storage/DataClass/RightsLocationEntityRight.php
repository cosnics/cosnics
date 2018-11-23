<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\Storage\DataManager;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsLocationEntityRight extends \Chamilo\Core\Rights\Domain\RightsLocationEntityRight
{

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'menu_rights_location_entity_right';
    }
}
