<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Storage\DataManager;

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
        return 'weblcms_rights_location';
    }
}
