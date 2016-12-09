<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    public function get_available_rights($location)
    {
        return WeblcmsRights::get_available_rights($location);
    }

    public function get_additional_parameters()
    {
        array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
