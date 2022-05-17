<?php
namespace Chamilo\Core\Admin\Announcement\Storage\DataClass;

/**
 * @package Chamilo\Core\Admin\Announcement\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Libraries\Rights\Domain\RightsLocation
{

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'admin_announcement_rights_location';
    }
}
