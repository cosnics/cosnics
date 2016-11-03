<?php
namespace Chamilo\Core\Admin\Announcement\Storage\DataClass;

use Chamilo\Core\Admin\Announcement\Storage\DataManager;

class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }
}
