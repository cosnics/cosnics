<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    public function get_available_rights($location)
    {
        return WeblcmsRights::get_available_rights($location);
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID);
    }
}
