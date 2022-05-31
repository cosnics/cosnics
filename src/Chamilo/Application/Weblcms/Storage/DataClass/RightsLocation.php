<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{

    /**
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'weblcms_rights_location';
    }
}
