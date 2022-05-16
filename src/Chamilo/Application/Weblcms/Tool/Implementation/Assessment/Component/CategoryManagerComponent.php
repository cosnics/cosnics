<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class CategoryManagerComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID;

        return $additionalParameters;
    }
}
