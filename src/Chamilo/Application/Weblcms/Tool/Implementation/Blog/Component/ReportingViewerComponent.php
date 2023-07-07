<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Blog\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Blog\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ReportingViewerComponent extends Manager
{

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE
                    )
                ), Translation::get('BlogToolBrowserComponent')
            )
        );

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => Request::get(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                        )
                    )
                ), Translation::get('BlogToolViewerComponent')
            )
        );
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_COMPLEX_ID;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_COURSE;

        return parent::getAdditionalParameters($additionalParameters);
    }
}
