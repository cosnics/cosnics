<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Libraries\Platform\Session\Request;

class DeleterComponent extends Manager
{

    public function run()
    {
        $id = Request::get(\Chamilo\Core\Repository\External\Manager::PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->delete_external_repository_object($id);
    }
}
