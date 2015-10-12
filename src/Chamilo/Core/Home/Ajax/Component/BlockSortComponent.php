<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class BlockSortComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_COLUMN = 'column';
    const PARAM_ORDER = 'order';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_COLUMN, self :: PARAM_ORDER);
    }

    public function get_blocks()
    {
        $block_data = explode('&', $this->getPostDataValue(self :: PARAM_ORDER));
        $blocks = array();
        
        foreach ($block_data as $block)
        {
            $block_split = explode('=', $block);
            $blocks[] = $block_split[1];
        }
        
        return $blocks;
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
        
        $column_data = explode('_', $this->getPostDataValue(self :: PARAM_COLUMN));
        $blocks = $this->get_blocks();
        
        $column = DataManager :: retrieve_by_id(Column :: class_name(), intval($column_data[2]));
        
        if ($column->getUserId() == $user_id)
        {
            $errors = 0;
            $i = 1;
            
            foreach ($blocks as $block_id)
            {
                $block = DataManager :: retrieve_by_id(Block :: class_name(), intval($block_id));
                
                if ($block)
                {
                    $block->setParentId($column->get_id());
                    $block->setSort($i);
                    
                    if (! $block->update())
                    {
                        $errors ++;
                    }
                    
                    $i ++;
                }
            }
            
            if ($errors > 0)
            {
                JsonAjaxResult :: error(409, Translation :: get('OneOrMoreBlocksNotUpdated'));
            }
            else
            {
                JsonAjaxResult :: success();
            }
        }
        else
        {
            JsonAjaxResult :: not_allowed();
        }
    }
}
