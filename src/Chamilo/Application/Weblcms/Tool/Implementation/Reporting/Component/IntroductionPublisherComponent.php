<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

class IntroductionPublisherComponent extends Manager
{

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE)), 
                Translation::get('ReportingToolBrowserComponent')));
    }
}
