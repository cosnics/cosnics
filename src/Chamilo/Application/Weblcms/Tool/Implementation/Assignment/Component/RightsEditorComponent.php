<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class RightsEditorComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

        return parent::get_additional_parameters($additionalParameters);
    }

    public function get_available_rights($location)
    {
        return WeblcmsRights::get_available_rights($location);
    }
}
