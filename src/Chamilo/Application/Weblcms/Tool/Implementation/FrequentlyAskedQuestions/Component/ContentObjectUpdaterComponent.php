<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ContentObjectUpdaterComponent extends Manager
{

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE)), 
                Translation::get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW, 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => Request::get(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))), 
                Translation::get('ViewerComponent')));
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
