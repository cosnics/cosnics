<?php
namespace Chamilo\Core\Lynx;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Page;

abstract class Manager extends Application
{
    const APPLICATION_NAME = 'lynx';
    const ACTION_BROWSE = 'Browser';
    const ACTION_SOURCE = 'Source';
    const ACTION_REMOTE = 'Remote';
    const ACTION_UPGRADE = 'Upgrader';
    const ACTION_CONTENT_OBJECT_UPGRADE = 'ContentObjectUpgrader';
    const ACTION_APPLICATION_UPGRADE = 'ApplicationUpgrader';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::__construct()
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $application = null)
    {
        parent :: __construct($request, $user, $application);

        Page :: getInstance()->setSection('Chamilo\Core\Admin');
    }
}
