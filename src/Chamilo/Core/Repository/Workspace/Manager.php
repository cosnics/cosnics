<?php
namespace Chamilo\Core\Repository\Workspace;

use Chamilo\Core\Repository\Service\WorkspaceExtensionManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Repository\Workspace
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_BROWSE_PERSONAL = 'PersonalBrowser';
    public const ACTION_BROWSE_SHARED = 'SharedBrowser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DEFAULT = 'Default';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_FAVOURITE = 'Favourite';
    public const ACTION_PUBLISH = 'Publisher';
    public const ACTION_RIGHTS = 'Rights';
    public const ACTION_SHARE = 'Share';
    public const ACTION_UNSHARE = 'Unshare';
    public const ACTION_UPDATE = 'Updater';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE_PERSONAL;

    public const PARAM_ACTION = 'workspace_action';
    public const PARAM_BROWSER_SOURCE = 'browser_source';
    public const PARAM_SELECTED_WORKSPACE_ID = 'selected_workspace_id';
    public const PARAM_WORKSPACE_ID = 'workspace_id';

    public function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    public function getWorkspaceExtensionManager(): WorkspaceExtensionManager
    {
        return $this->getService(WorkspaceExtensionManager::class);
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }
}
