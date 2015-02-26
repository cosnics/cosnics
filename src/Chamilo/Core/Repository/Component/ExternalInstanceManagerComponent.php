<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Structure\Header;

class ExternalInstanceManagerComponent extends Manager
{

    public function run()
    {
        Header :: get_instance()->set_section('external_repository');
        \Chamilo\Core\Repository\External\Manager :: launch($this);
    }
}
