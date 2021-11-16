<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;

class MoverComponent extends Manager
{

    public function get_move_direction()
    {
        return Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION);
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
