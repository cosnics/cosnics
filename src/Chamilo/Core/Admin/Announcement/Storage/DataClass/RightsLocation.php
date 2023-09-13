<?php
namespace Chamilo\Core\Admin\Announcement\Storage\DataClass;

use Chamilo\Core\Admin\Announcement\Manager;

/**
 * @package Chamilo\Core\Admin\Announcement\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Libraries\Rights\Domain\RightsLocation
{
    public const CONTEXT = Manager::CONTEXT;

    public static function getStorageUnitName(): string
    {
        return 'admin_announcement_rights_location';
    }
}
