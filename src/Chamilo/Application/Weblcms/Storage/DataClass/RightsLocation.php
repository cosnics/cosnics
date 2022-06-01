<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{

    /**
     *
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_rights_location';
    }
}
