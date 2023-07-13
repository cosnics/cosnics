<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

class CheckTitleComponent extends Manager
{
    public const PARAM_CONTENT_OBJECT_ID = 'content_object_id';

    public const PARAM_PARENT_ID = 'parent_id';

    public const PARAM_TITLE = 'title';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */

    public function run()
    {
        $title = $this->getPostDataValue(self::PARAM_TITLE);
        $parent_id = $this->getPostDataValue(self::PARAM_PARENT_ID);
        $content_object_id = $this->getRequest()->request->get(self::PARAM_CONTENT_OBJECT_ID);

        if (!DataManager::content_object_title_exists($title, $parent_id, $content_object_id))
        {
            JsonAjaxResult::success();
        }
        else
        {
            JsonAjaxResult::error(409, Translation::get('TitleAlreadyExists'));
        }
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_TITLE, self::PARAM_PARENT_ID];
    }
}
