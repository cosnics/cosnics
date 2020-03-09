<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Lynx
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    const ACTION_ACTIVATE = 'Activator';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DEACTIVATE = 'Deactivator';
    const ACTION_INSTALL = 'Installer';
    const ACTION_REMOVE = 'Remover';
    const ACTION_VIEW = 'Viewer';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_ACTION = 'manager_action';
    const PARAM_ACTIVATE_SELECTED = 'activate';
    const PARAM_CONTEXT = 'context';
    const PARAM_DEACTIVATE_SELECTED = 'deactivate';
    const PARAM_INSTALL_SELECTED = 'install';
    const PARAM_INSTALL_TYPE = 'type';
    const PARAM_PACKAGE = 'package';
    const PARAM_REGISTRATION = 'registration';
    const PARAM_REGISTRATION_TYPE = 'type';
    const PARAM_SECTION = 'section';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }
}