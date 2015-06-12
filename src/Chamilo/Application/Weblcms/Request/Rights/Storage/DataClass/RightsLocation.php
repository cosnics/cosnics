<?php
namespace Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Rights\Storage\DataManager;

class RightsLocation extends \Chamilo\Core\Rights\RightsLocation
{

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }
}
