<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class CheckTitleComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_TITLE = 'title';
    const PARAM_PARENT_ID = 'parent_id';
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_TITLE, self::PARAM_PARENT_ID);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $title = $this->getPostDataValue(self::PARAM_TITLE);
        $parent_id = $this->getPostDataValue(self::PARAM_PARENT_ID);
        $content_object_id = Request::post(self::PARAM_CONTENT_OBJECT_ID);
        
        if (! DataManager::content_object_title_exists($title, $parent_id, $content_object_id))
        {
            JsonAjaxResult::success();
        }
        else
        {
            JsonAjaxResult::error(409, Translation::get('TitleAlreadyExists'));
        }
    }
}
