<?php
namespace Chamilo\Core\Repository\Quota\Rights\Storage\DataClass;

use Chamilo\Core\Repository\Quota\Rights\Storage\DataManager;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }
}
