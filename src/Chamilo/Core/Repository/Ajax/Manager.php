<?php
namespace Chamilo\Core\Repository\Ajax;

use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\Repository\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_CATEGORY_MENU_FEED = 'XmlRepositoryCategoryMenuFeed';
    public const ACTION_DELETE_FILE = 'DeleteFile';
    public const ACTION_IMPORT_FILE = 'ImportFile';
    public const ACTION_THUMBNAIL = 'Thumbnail';

    public const CONTEXT = __NAMESPACE__;

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }
}
