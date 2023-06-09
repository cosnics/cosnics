<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class BlockMoveComponent extends Manager
{
    public const PARAM_COLUMN = 'column';
    public const PARAM_BLOCK = 'block';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return array(self::PARAM_COLUMN, self::PARAM_BLOCK);
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
        
        $block = DataManager::retrieve_by_id(Block::class, intval($this->getPostDataValue(self::PARAM_BLOCK)));

        if(!$block instanceof Block)
        {
            throw new ObjectNotExistException(Translation::getInstance()->getTranslation('Block'));
        }

        if ($block->getUserId() == $user_id)
        {
            $block->setParentId($this->getPostDataValue(self::PARAM_COLUMN));
            
            if ($block->update())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::general_error(Translation::get('BlockNotMovedToTab'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
