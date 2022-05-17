<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;

class ContentObjectUpdaterComponent extends Manager
{

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;

        return parent::get_additional_parameters($additionalParameters);
    }
}
