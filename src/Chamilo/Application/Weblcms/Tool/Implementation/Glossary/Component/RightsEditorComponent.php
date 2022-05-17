<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID;

        return parent::get_additional_parameters($additionalParameters);
    }

    public function get_available_rights($location)
    {
        return WeblcmsRights::get_available_rights($location);
    }
}
