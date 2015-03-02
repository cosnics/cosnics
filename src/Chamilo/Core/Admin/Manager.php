<?php
namespace Chamilo\Core\Admin;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 * The admin allows the platform admin to configure certain aspects of his platform
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'admin';
    const PARAM_WEB_APPLICATION = 'web_application';
    const PARAM_CONTEXT = 'context';
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_EDIT_SELECTED = 'edit_selected';
    const ACTION_ADMIN_BROWSER = 'browser';
    const ACTION_LANGUAGE = 'language';
    const ACTION_CONFIGURE_PLATFORM = 'configurer';
    const ACTION_WHOIS_ONLINE = 'whois_online';
    const ACTION_DIAGNOSE = 'diagnoser';
    const ACTION_VIEW_LOGS = 'log_viewer';
    const ACTION_IMPORTER = 'importer';
    const ACTION_SYSTEM_ANNOUNCEMENTS = 'announcer';
    const DEFAULT_ACTION = self :: ACTION_ADMIN_BROWSER;
}
