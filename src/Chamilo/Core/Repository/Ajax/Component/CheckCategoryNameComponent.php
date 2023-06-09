<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

class CheckCategoryNameComponent extends Manager
{
    public const PARAM_NAME = 'name';
    public const PARAM_PARENT_ID = 'parent_id';

    public const PARAM_WORKSPACE_ID = 'workspace_id';

    public const PARAM_WORKSPACE_TYPE = 'workspace_type';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */

    public function run()
    {
        $category_name = $this->getPostDataValue(self::PARAM_NAME);
        $parent_id = $this->getPostDataValue(self::PARAM_PARENT_ID);

        $workspace =
            $this->getWorkspaceService()->getWorkspaceByIdentifier($this->getPostDataValue(self::PARAM_WORKSPACE_ID));

        if (!DataManager::check_category_name($workspace, $parent_id, $category_name))
        {
            JsonAjaxResult::success();
        }
        else
        {
            JsonAjaxResult::error(409, Translation::get('CategoryAlreadyExists'));
        }
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_NAME, self::PARAM_PARENT_ID, self::PARAM_WORKSPACE_TYPE, self::PARAM_WORKSPACE_ID];
    }
}
