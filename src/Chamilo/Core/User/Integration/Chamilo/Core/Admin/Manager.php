<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Admin;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Actions;
use Chamilo\Core\Admin\ActionsSupportInterface;
use Chamilo\Core\Admin\ImportActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Action;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class Manager implements ActionsSupportInterface, ImportActionsInterface
{

    public static function getActions(): Actions
    {
        $links = [];

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_BROWSE_USERS
            )
        );
        $links[] = new Action(
            Translation::get('ListDescription'), Translation::get('List'),
            new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        if (Configuration::getInstance()->get_setting(
                array(\Chamilo\Core\User\Manager::context(), 'allow_registration')
            ) == 2)
        {
            $redirect = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                    \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_USER_APPROVAL_BROWSER
                )
            );
            $links[] = new Action(
                Translation::get('ApproveListDescription'), Translation::get('ApproveList'),
                new FontAwesomeGlyph('list', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
            );
        }

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_CREATE_USER
            )
        );
        $links[] = new Action(
            Translation::get('CreateDescription'), Translation::get('Create', null, StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('plus', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_EXPORT_USERS
            )
        );
        $links[] = new Action(
            Translation::get('ExportDescription'), Translation::get('Export', null, StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('download', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_IMPORT_USERS
            )
        );
        $links[] = new Action(
            Translation::get('ImportDescription'), Translation::get('Import', null, StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('upload', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_BUILD_USER_FIELDS
            )
        );
        $links[] = new Action(
            Translation::get('BuildUserFieldsDescription'), Translation::get('BuildUserFields'),
            new FontAwesomeGlyph('user', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_BROWSE_USERS
            )
        );
        $info = new Actions(\Chamilo\Core\User\Manager::context(), $links);
        $info->set_search($redirect->getUrl());

        return $info;
    }

    public static function get_import_actions()
    {
        $links = [];

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::context(),
                \Chamilo\Core\User\Manager::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_IMPORT_USERS
            )
        );
        $links[] = new Action(
            Translation::get('ImportDescription'), Translation::get('Import', null, StringUtilities::LIBRARIES),
            new FontAwesomeGlyph('user', array('fa-fw', 'fa-2x'), null, 'fas'), $redirect->getUrl()
        );

        return $links;
    }
}
