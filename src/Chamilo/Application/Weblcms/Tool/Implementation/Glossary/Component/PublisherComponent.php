<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Glossary\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

class PublisherComponent extends Manager
{

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_BROWSE)), 
                Translation::get('BrowserComponent')));
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES, 
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_WORKSPACE_ID);
    }
}
