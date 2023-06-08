<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockSortComponent extends Manager
{
    public const PARAM_COLUMN = 'column';
    public const PARAM_ORDER = 'order';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_COLUMN, self::PARAM_ORDER);
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
        
        $columnId = $this->getPostDataValue(self::PARAM_COLUMN);
        parse_str($this->getPostDataValue(self::PARAM_ORDER), $blocks);
        
        $column = DataManager::retrieve_by_id(Column::class, $columnId);
        
        if ($column->getUserId() == $userId)
        {
            $errors = 0;
            
            foreach ($blocks[self::PARAM_ORDER] as $sortOrder => $blockId)
            {
                $block = DataManager::retrieve_by_id(Block::class, intval($blockId));
                
                if ($block)
                {
                    $block->setParentId($column->get_id());
                    $block->setSort($sortOrder + 1);
                    
                    if (! $block->update())
                    {
                        $errors ++;
                    }
                }
            }
            
            if ($errors > 0)
            {
                JsonAjaxResult::error(409, Translation::get('OneOrMoreBlocksNotUpdated'));
            }
            else
            {
                JsonAjaxResult::success();
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
