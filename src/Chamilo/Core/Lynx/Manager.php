<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

abstract class Manager extends Application
{
    const ACTION_BROWSE = 'Browser';
    const ACTION_SOURCE = 'Source';
    const ACTION_REMOTE = 'Remote';
    const ACTION_UPGRADE = 'Upgrader';
    const ACTION_CONTENT_OBJECT_UPGRADE = 'ContentObjectUpgrader';
    const ACTION_APPLICATION_UPGRADE = 'ApplicationUpgrader';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent:: __construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }
}