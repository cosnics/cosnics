<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class ContentObjectUpdaterComponent extends Manager implements DelegateComponent
{

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;
        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
