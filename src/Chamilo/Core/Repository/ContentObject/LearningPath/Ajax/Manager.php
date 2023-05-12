<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax;

use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * Class Manager
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
abstract class Manager extends AjaxManager
{
    public const CONTEXT = __NAMESPACE__;
    public const PARAM_WORKSPACE_ID = 'workspace_id';

    /**
     * @return ContentObjectRepository | object
     */
    protected function getContentObjectRepository()
    {
        return $this->getService(ContentObjectRepository::class);
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    /**
     * Returns the workspace from the request
     *
     * @throws NotAllowedException
     */
    protected function getWorkspaceFromRequest(): Workspace
    {
        $workspace = $this->getCurrentWorkspace();

        if (!$this->getWorkspaceRightsService()->canViewContentObjects($this->getUser(), $workspace))
        {
            throw new NotAllowedException();
        }

        return $workspace;
    }

    protected function getWorkspaceRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }
}
