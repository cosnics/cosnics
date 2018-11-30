<?php
namespace Chamilo\Core\Admin\Announcement\Storage\DataClass;

use Chamilo\Core\Admin\Announcement\Storage\DataManager;

/**
 * @package Chamilo\Core\Admin\Announcement\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsLocation extends \Chamilo\Libraries\Rights\Domain\RightsLocation
{

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'admin_announcement_rights_location';
    }
}
