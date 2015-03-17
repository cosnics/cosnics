<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataManager;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }
}
