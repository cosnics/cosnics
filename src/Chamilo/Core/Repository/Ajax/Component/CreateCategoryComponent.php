<?php

namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Service\CategoryService;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\Ajax\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreateCategoryComponent extends Manager
{
    const PARAM_NAME = 'name';
    const PARAM_PARENT_ID = 'parent_id';
    const PARAM_WORKSPACE_ID = 'workspace_id';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_NAME);
    }

    /**
     * @return string|JsonResponse
     */
    public function run()
    {
        $name = $this->getRequest()->getFromPost(self::PARAM_NAME);
        $workspaceId = $this->getRequest()->getFromPost(self::PARAM_WORKSPACE_ID);
        $parentId = $this->getRequest()->getFromPost(self::PARAM_PARENT_ID);

        try
        {
            $workspace =
                $this->getWorkspaceService()->determineWorkspaceForUserByIdentifier($this->getUser(), $workspaceId);

            if (!$this->getRightsService()->canAddContentObjects($this->getUser(), $workspace))
            {
                throw new NotAllowedException();
            }

            $category = $this->getCategoryService()->createCategoryInWorkspace($name, $workspace, $parentId);

            return new JsonResponse(['id' => $category->getId(), 'name' => $category->get_name()], 200);
        }
        catch(\Exception $ex)
        {
            return $this->handleException($ex);
        }
    }

    /**
     * @return WorkspaceService
     */
    protected function getWorkspaceService()
    {
        return $this->getService(WorkspaceService::class);
    }

    /**
     * @return RightsService
     */
    protected function getRightsService()
    {
        return $this->getService(RightsService::class);
    }

    /**
     * @return CategoryService
     */
    protected function getCategoryService()
    {
        return $this->getService(CategoryService::class);
    }
}
