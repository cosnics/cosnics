<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_rights_location_entity_right';
    }
}