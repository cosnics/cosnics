<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

class Manager implements ActionsSupportInterface
{

    public static function get_actions()
    {
        $info = new Actions(\Chamilo\Core\Repository\Manager::context());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Instance\Manager::context(), 
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Instance\Manager::ACTION_BROWSE));
        $links[] = new DynamicAction(
            Translation::get('ManageExternalInstances'), 
            Translation::get('ManageExternalInstancesDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/ExternalInstance'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(), 
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_LINK_SCHEMAS));
        $links[] = new DynamicAction(
            Translation::get('LinkSchemas'), 
            Translation::get('LinkSchemasDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(), 
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_LINK_PROVIDERS));
        $links[] = new DynamicAction(
            Translation::get('LinkProviders'), 
            Translation::get('LinkProvidersDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Manager::context(), 
                \Chamilo\Core\Repository\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_BROWSE_CONTENT_OBJECTS));
        
        $info->set_search($redirect->getUrl());
        $info->set_links($links);
        
        return $info;
    }
}
