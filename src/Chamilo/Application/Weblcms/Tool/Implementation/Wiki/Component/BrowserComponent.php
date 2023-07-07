<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Wiki\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class BrowserComponent extends Manager
{

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
