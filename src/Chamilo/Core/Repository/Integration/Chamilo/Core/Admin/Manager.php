<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface
{

    public static function getActions(): Actions
    {
        $info = new Actions(\Chamilo\Core\Repository\Manager::context());

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Instance\Manager::context(),
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Instance\Manager::ACTION_BROWSE
            )
        );
        $links[] = new Action(
            Translation::get('ManageExternalInstancesDescription'), Translation::get('ManageExternalInstances'),
            new FontAwesomeGlyph('globe', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(),
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_LINK_SCHEMAS
            )
        );
        $links[] = new Action(
            Translation::get('LinkSchemasDescription'), Translation::get('LinkSchemas'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(),
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_LINK_PROVIDERS
            )
        );
        $links[] = new Action(
            Translation::get('LinkProvidersDescription'), Translation::get('LinkProviders'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(),
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS
            )
        );

        $info->set_search($redirect->getUrl());
        $info->set_links($links);

        return $info;
    }
}
