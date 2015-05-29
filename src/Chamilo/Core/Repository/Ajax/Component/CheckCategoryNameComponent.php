<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

class CheckCategoryNameComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_NAME = 'name';
    const PARAM_PARENT_ID = 'parent_id';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_NAME, self :: PARAM_PARENT_ID);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $category_name = $this->getPostDataValue(self :: PARAM_NAME);
        $parent_id = $this->getPostDataValue(self :: PARAM_PARENT_ID);
        
        if (! DataManager :: check_category_name(Session :: get_user_id(), $parent_id, $category_name))
        {
            JsonAjaxResult :: success();
        }
        else
        {
            JsonAjaxResult :: error(409, Translation :: get('CategoryAlreadyExists'));
        }
    }
}
