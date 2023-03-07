<?php
namespace Chamilo\Core\Repository\Workspace\Rights;

use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
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
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_RIGHTS = 'Rights';
    public const ACTION_UPDATE = 'Updater';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_CREATE;

    public const PARAM_ACTION = 'rights_action';
    public const PARAM_ENTITY_RELATION_ID = 'entity_relation_id';

    public function getCurrentWorkspace(): ?Workspace
    {
        return $this->getWorkspaceService()->getWorkspaceByIdentifier($this->getCurrentWorkspaceIdentifier());
    }

    public function getCurrentWorkspaceIdentifier(): string
    {
        return $this->getRequest()->query->get(\Chamilo\Core\Repository\Workspace\Manager::PARAM_WORKSPACE_ID);
    }

    protected function getEntityRelationService(): EntityRelationService
    {
        return $this->getService(EntityRelationService::class);
    }

    protected function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }

    protected function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }
}
