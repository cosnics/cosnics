<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockDeleteComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_BLOCK = 'block';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_BLOCK);
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
        
        $block_data = explode('_', $this->getPostDataValue(self :: PARAM_BLOCK));
        
        $block = DataManager :: retrieve_by_id(Block :: class_name(), intval($block_data[2]));
        
        if ($block->get_user() == $user_id)
        {
            if ($block->delete())
            {
                JsonAjaxResult :: success();
            }
            else
            {
                JsonAjaxResult :: error(409, Translation :: get('BlockNotDeleted'));
            }
        }
        else
        {
            JsonAjaxResult :: not_allowed();
        }
    }
}
