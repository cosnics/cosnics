<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class AttachmentViewerComponent extends Manager
{

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
