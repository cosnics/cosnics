<?php
namespace Chamilo\Application\Survey\Rights\Storage\DataClass;

use Chamilo\Application\Survey\Rights\Storage\DataManager;

class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }
}
