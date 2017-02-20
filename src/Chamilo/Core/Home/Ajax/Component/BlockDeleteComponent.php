<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
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
        return array(self::PARAM_BLOCK);
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
        
        $block = DataManager::retrieve_by_id(Block::class_name(), $blockId);

        if(!$block instanceof Block)
        {
            throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Block'), $blockId);
        }
        
        if ($block->getUserId() == $userId)
        {
            if ($block->delete())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::error(409, Translation::get('BlockNotDeleted'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
