<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;

class ReportingViewerComponent extends Manager
{

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_COMPLEX_ID;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_COURSE;

        return parent::get_additional_parameters($additionalParameters);
    }
}
