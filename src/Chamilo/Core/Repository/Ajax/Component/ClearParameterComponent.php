<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ParameterFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

class ClearParameterComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_PARAMETER = 'parameter';
    const PARAM_URL = 'url';
    const PARAM_CURRENT_WORKSPACE_ID = 'current_workspace_id';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_PARAMETER, self::PARAM_URL);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $parameter = $this->getPostDataValue(self::PARAM_PARAMETER);
        $parameter = explode('_', $parameter, 2);
        
        try
        {
            if (count($parameter) == 2 && is_string($parameter[1]))
            {
                $currentWorkspaceIdentifier = $this->getRequest()->request->get(self::PARAM_CURRENT_WORKSPACE_ID);
                $currentWorkspaceIdentifier = $currentWorkspaceIdentifier ? $currentWorkspaceIdentifier : null;
                
                $workspaceService = new WorkspaceService(new WorkspaceRepository());
                $currentWorkspace = $workspaceService->determineWorkspaceForUserByIdentifier(
                    $this->get_user(), 
                    $currentWorkspaceIdentifier);
                
                ParameterFilterRenderer::factory(
                    FilterData::getInstance($currentWorkspace), 
                    $currentWorkspace, 
                    $parameter[1])->render();
                $url = FilterData::clean_url($currentWorkspace, $this->getPostDataValue(self::PARAM_URL));
                
                $result = new JsonAjaxResult();
                $result->set_property(self::PARAM_URL, $url);
                $result->set_result_code(200);
                $result->display();
            }
            else
            {
                JsonAjaxResult::general_error(Translation::get('NoParameterConfiguredToClear'));
            }
        }
        catch (\Exception $exception)
        {
            JsonAjaxResult::error(500);
        }
    }
}
