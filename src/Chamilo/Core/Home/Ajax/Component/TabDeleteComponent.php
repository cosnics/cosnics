<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class TabDeleteComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_TAB = 'tab';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_TAB);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $user_id = DataManager :: determine_user_id();
        
        if ($user_id === false)
        {
            JsonAjaxResult :: not_allowed();
        }
        
        $tab = DataManager :: retrieve_by_id(Tab :: class_name(), intval($this->getPostDataValue(self :: PARAM_TAB)));
        
        if ($tab->get_user() == $user_id && $tab->can_be_deleted())
        {
            if ($tab->delete())
            {
                JsonAjaxResult :: success();
            }
            else
            {
                JsonAjaxResult :: general_error(Translation :: get('TabNotDeleted'));
            }
        }
        else
        {
            JsonAjaxResult :: not_allowed();
        }
    }
}
