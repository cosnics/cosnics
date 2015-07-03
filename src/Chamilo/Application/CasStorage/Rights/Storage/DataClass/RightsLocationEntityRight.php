<?php
namespace Chamilo\Application\CasUser\Rights\Storage\DataClass;

use Chamilo\Application\CasUser\Rights\Storage\DataManager;

class RightsLocationEntityRight extends \Chamilo\Core\Rights\RightsLocationEntityRight
{

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }
}
