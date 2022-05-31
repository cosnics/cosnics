<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_rights_location_entity_right';
    }
}