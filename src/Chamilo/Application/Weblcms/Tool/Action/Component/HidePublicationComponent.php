<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

class HidePublicationComponent extends ToggleVisibilityComponent implements BreadcrumbLessComponentInterface
{

    public function get_hidden()
    {
        return 1;
    }
}
