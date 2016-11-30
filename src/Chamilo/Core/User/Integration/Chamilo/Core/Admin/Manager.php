<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Admin;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Core\Admin\ImportActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Manager implements ActionsSupportInterface, ImportActionsInterface
{

    public static function get_actions()
    {
        $links = array();
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_BROWSE_USERS));
        $links[] = new DynamicAction(
            Translation::get('List'), 
            Translation::get('ListDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/List'), 
            $redirect->getUrl());
        
        if (Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\User\Manager::context(), 'allow_registration')) == 2)
        {
            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                    \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_USER_APPROVAL_BROWSER));
            $links[] = new DynamicAction(
                Translation::get('ApproveList'), 
                Translation::get('ApproveListDescription'), 
                Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/List'), 
                $redirect->getUrl());
        }
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_CREATE_USER));
        $links[] = new DynamicAction(
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('CreateDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Add'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_EXPORT_USERS));
        $links[] = new DynamicAction(
            Translation::get('Export', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('ExportDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Export'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_IMPORT_USERS));
        $links[] = new DynamicAction(
            Translation::get('Import', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('ImportDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_BUILD_USER_FIELDS));
        $links[] = new DynamicAction(
            Translation::get('BuildUserFields'), 
            Translation::get('BuildUserFieldsDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Build'), 
            $redirect->getUrl());
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_BROWSE_USERS));
        $info = new Actions(\Chamilo\Core\User\Manager::context(), $links);
        $info->set_search($redirect->getUrl());
        
        return $info;
    }

    public static function get_import_actions()
    {
        $links = array();
        
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(), 
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_IMPORT_USERS));
        $links[] = new DynamicAction(
            Translation::get('Import', null, Utilities::COMMON_LIBRARIES), 
            Translation::get('ImportDescription'), 
            Theme::getInstance()->getImagePath(__NAMESPACE__, 'Admin/Import'), 
            $redirect->getUrl());
        
        return $links;
    }
}
