<?php
namespace Chamilo\Core\Admin;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * The admin allows the platform admin to configure certain aspects of his platform
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
abstract class Manager extends Application
{
    const PARAM_WEB_APPLICATION = 'web_application';
    const PARAM_CONTEXT = 'context';
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_EDIT_SELECTED = 'edit_selected';
    const ACTION_ADMIN_BROWSER = 'Browser';
    const ACTION_LANGUAGE = 'Language';
    const ACTION_CONFIGURE_PLATFORM = 'Configurer';
    const ACTION_WHOIS_ONLINE = 'WhoisOnline';
    const ACTION_DIAGNOSE = 'Diagnoser';
    const ACTION_VIEW_LOGS = 'LogViewer';
    const ACTION_IMPORTER = 'Importer';
    const ACTION_SYSTEM_ANNOUNCEMENTS = 'Announcer';
    const DEFAULT_ACTION = self :: ACTION_ADMIN_BROWSER;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }
}
