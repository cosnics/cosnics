<?php
namespace Chamilo\Core\Group\Integration\Chamilo\Core\Admin;

use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicAction;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Manager implements ActionsSupportInterface
{

    public static function get_actions()
    {
        $links = [];

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Group\Manager::ACTION_BROWSE_GROUPS
            )
        );
        $links[] = new DynamicAction(
            Translation::get('List', null, Utilities::COMMON_LIBRARIES), Translation::get('ListDescription'),
            new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Group\Manager::ACTION_CREATE_GROUP,
                \Chamilo\Core\Group\Manager::PARAM_GROUP_ID => 0
            )
        );
        $links[] = new DynamicAction(
            Translation::get('Create', null, Utilities::COMMON_LIBRARIES), Translation::get('CreateDescription'),
            new FontAwesomeGlyph('plus', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl(), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Group\Manager::ACTION_EXPORT
            )
        );
        $links[] = new DynamicAction(
            Translation::get('Export', null, Utilities::COMMON_LIBRARIES), Translation::get('ExportDescription'),
            new FontAwesomeGlyph('download', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl(),
            $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Group\Manager::ACTION_IMPORT
            )
        );
        $links[] = new DynamicAction(
            Translation::get('Import', null, Utilities::COMMON_LIBRARIES), Translation::get('ImportDescription'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl(),
            $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Group\Manager::ACTION_IMPORT_GROUP_USERS
            )
        );
        $links[] = new DynamicAction(
            Translation::get('ImportGroupUsers'), Translation::get('ImportGroupUsersDescription'),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl(),
            $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Group\Manager::ACTION_BROWSE_GROUPS
            )
        );
        $info = new Actions(\Chamilo\Core\Group\Manager::context());
        $info->set_links($links);
        $info->set_search($redirect->getUrl());

        return $info;
    }
}
