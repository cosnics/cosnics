<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

class CheckCategoryNameComponent extends Manager
{
    const PARAM_NAME = 'name';
    const PARAM_PARENT_ID = 'parent_id';
    const PARAM_WORKSPACE_TYPE = 'workspace_type';
    const PARAM_WORKSPACE_ID = 'workspace_id';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_NAME, self::PARAM_PARENT_ID, self::PARAM_WORKSPACE_TYPE, self::PARAM_WORKSPACE_ID);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $category_name = $this->getPostDataValue(self::PARAM_NAME);
        $parent_id = $this->getPostDataValue(self::PARAM_PARENT_ID);
        
        if ($this->getPostDataValue(self::PARAM_WORKSPACE_TYPE) == PersonalWorkspace::WORKSPACE_TYPE)
        {
            $workspace = new PersonalWorkspace($this->get_user());
        }
        else
        {
            $workspaceService = new WorkspaceService(new WorkspaceRepository());
            $workspace = $workspaceService->getWorkspaceByIdentifier($this->getPostDataValue(self::PARAM_WORKSPACE_ID));
        }
        
        if (! DataManager::check_category_name($workspace, $parent_id, $category_name))
        {
            JsonAjaxResult::success();
        }
        else
        {
            JsonAjaxResult::error(409, Translation::get('CategoryAlreadyExists'));
        }
    }
}
