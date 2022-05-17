<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ReportingViewerComponent extends Manager
{

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE
                    )
                ), Translation::get('LearningPathToolBrowserComponent')
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
                ), Translation::get('LearningPathToolViewerComponent')
            )
        );
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_COMPLEX_ID;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Tool\Manager::PARAM_TEMPLATE_NAME;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_COURSE;

        return parent::get_additional_parameters($additionalParameters);
    }
}
