<?php
namespace Chamilo\Core\Admin;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * The admin allows the platform admin to configure certain aspects of his platform
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
abstract class Manager extends Application
{
    public const ACTION_ADMIN_BROWSER = 'Browser';
    public const ACTION_CONFIGURE_PLATFORM = 'Configurer';
    public const ACTION_DIAGNOSE = 'Diagnoser';
    public const ACTION_IMPORTER = 'Importer';
    public const ACTION_LANGUAGE = 'Language';
    public const ACTION_SYSTEM_ANNOUNCEMENTS = 'Announcer';
    public const ACTION_VIEW_LOGS = 'LogViewer';
    public const ACTION_WHOIS_ONLINE = 'WhoisOnline';

    public const DEFAULT_ACTION = self::ACTION_ADMIN_BROWSER;

    public const PARAM_CONTEXT = 'context';
    public const PARAM_DELETE_SELECTED = 'delete_selected';
    public const PARAM_EDIT_SELECTED = 'edit_selected';
    public const PARAM_USER_ID = 'user_id';
    public const PARAM_WEB_APPLICATION = 'web_application';
}
