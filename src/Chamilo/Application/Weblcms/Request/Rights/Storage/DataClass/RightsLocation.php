<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Rights\Manager;

class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{
    public const CONTEXT = Manager::CONTEXT;

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_request_rights_location';
    }
}
