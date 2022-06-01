<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Rights\Storage\DataManager;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_request_rights_location_entity_right';
    }
}