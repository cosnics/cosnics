<?php
namespace Chamilo\Core\Repository\External\Action\Component;

use Chamilo\Core\Repository\External\Action\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ImporterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $id = Request::get(\Chamilo\Core\Repository\External\Manager::PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->retrieve_external_repository_object($id);
        $succes = $this->import_external_repository_object($object);
        
        $params = [];
        $params[\Chamilo\Core\Repository\External\Manager::PARAM_ACTION] = '';
        
        if ($succes)
        {
            $this->redirectWithMessage(Translation::get('Succes', null, StringUtilities::LIBRARIES), false, $params);
        }
        else
        {
            $this->redirectWithMessage(Translation::get('Failed', null, StringUtilities::LIBRARIES), true, $params);
        }
    }
}
