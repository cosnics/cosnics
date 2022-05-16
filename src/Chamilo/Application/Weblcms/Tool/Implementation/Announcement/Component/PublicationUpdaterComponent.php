<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class PublicationUpdaterComponent extends Manager
{

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;
        return $additionalParameters;
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
