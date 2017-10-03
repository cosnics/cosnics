<?php
namespace Chamilo\Core\Lynx\Manager;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * $Id: package_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 *
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'manager_action';
    const PARAM_REGISTRATION = 'registration';
    const PARAM_ACTIVATE_SELECTED = 'activate';
    const PARAM_DEACTIVATE_SELECTED = 'deactivate';
    const PARAM_INSTALL_SELECTED = 'install';
    const PARAM_PACKAGE = 'package';
    const PARAM_INSTALL_TYPE = 'type';
    const PARAM_SECTION = 'section';
    const PARAM_CONTEXT = 'context';
    const PARAM_REGISTRATION_TYPE = 'type';
    const ACTION_BROWSE = 'Browser';
    const ACTION_ACTIVATE = 'Activator';
    const ACTION_DEACTIVATE = 'Deactivator';
    const ACTION_INSTALL = 'Installer';
    const ACTION_REMOVE = 'Remover';
    const ACTION_VIEW = 'Viewer';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
}
