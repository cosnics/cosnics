<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Rights\Storage\DataManager;

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
    public static function getTableName(): string
    {
        return 'weblcms_request_rights_location';
    }
}
