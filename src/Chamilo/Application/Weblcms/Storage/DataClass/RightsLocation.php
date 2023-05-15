<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;

class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_rights_location';
    }
}
