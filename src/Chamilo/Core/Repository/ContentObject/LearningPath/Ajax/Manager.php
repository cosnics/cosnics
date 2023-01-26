<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;

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

    /**
     * @return RightsService
     */
    protected function getRightsService()
    {
        return RightsService::getInstance();
    }

    /**
     * Returns the workspace from the request
     *
     * @return WorkspaceInterface
     * @throws NotAllowedException
     * @throws ObjectNotExistException
     */
    protected function getWorkspaceFromRequest(): WorkspaceInterface
    {
        $workspaceService = new WorkspaceService(new WorkspaceRepository());

        $workspaceId = $this->getRequest()->get(self::PARAM_WORKSPACE_ID);
        $workspace = $workspaceService->determineWorkspaceForUserByIdentifier($this->getUser(), $workspaceId);

        if (!$workspace instanceof WorkspaceInterface)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('Workspace'), $workspaceId
            );
        }

        $rightsService = RightsService::getInstance();
        if (!$rightsService->canViewContentObjects($this->getUser(), $workspace))
        {
            throw new NotAllowedException();
        }

        return $workspace;
    }
}
