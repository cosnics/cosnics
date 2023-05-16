<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Rights\Manager;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_request_rights_location_entity_right';
    }
}