<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class BrowserComponent extends Manager
{
    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {

    }
}
