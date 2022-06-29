<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class BlockEditComponent extends Manager
{
    const PARAM_BLOCK = 'block';
    const PARAM_TITLE = 'title';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_BLOCK, self::PARAM_TITLE);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $user_id = DataManager::determine_user_id();
        
        if ($user_id === false)
        {
            JsonAjaxResult::not_allowed();
        }
        
        $block = intval($this->getPostDataValue(self::PARAM_BLOCK));
        $title = $this->getPostDataValue(self::PARAM_TITLE);
        
        $block = DataManager::retrieve_by_id(Block::class, $block);

        if(!$block instanceof Block)
        {
            throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Block'));
        }

        if ($block->getUserId() == $user_id)
        {
            $block->setTitle($title);
            if ($block->update())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::general_error(Translation::get('BlockNotUpdated'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
