<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockVisibilityComponent extends Manager
{
    const PARAM_BLOCK = 'block';
    const PARAM_VISIBILITY = 'visibility';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_BLOCK, self::PARAM_VISIBILITY);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $userId = DataManager::determine_user_id();
        
        if ($userId === false)
        {
            JsonAjaxResult::not_allowed();
        }
        
        $blockId = $this->getPostDataValue(self::PARAM_BLOCK);
        
        $block = DataManager::retrieve_by_id(Block::class, $blockId);
        
        if ($block->getUserId() == $userId)
        {
            $block->setVisibility(!($this->getPostDataValue(self::PARAM_VISIBILITY) == 'false'));
            
            if ($block->update())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::error(409, Translation::get('BlockNotUpdated'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
