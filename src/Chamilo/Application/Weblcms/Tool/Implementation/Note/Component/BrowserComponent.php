<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Note\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Note\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class BrowserComponent extends Manager
{

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::get_additional_parameters($additionalParameters);
    }
}
