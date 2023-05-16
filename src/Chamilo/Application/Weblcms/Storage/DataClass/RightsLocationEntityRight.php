<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_rights_location_entity_right';
    }
}