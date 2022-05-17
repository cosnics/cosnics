<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataManager;

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
        return 'weblcms_rights_location_entity_right';
    }
}