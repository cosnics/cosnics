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

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $userId = DataManager :: determine_user_id();

        if ($userId === false)
        {
            JsonAjaxResult :: not_allowed();
        }

        $columnId = $this->getPostDataValue(self :: PARAM_COLUMN);
        parse_str($this->getPostDataValue(self :: PARAM_ORDER), $blocks);

        $column = DataManager :: retrieve_by_id(Column :: class_name(), $columnId);

        if ($column->getUserId() == $userId)
        {
            $errors = 0;

            foreach ($blocks[self :: PARAM_ORDER] as $sortOrder => $blockId)
            {
                $block = DataManager :: retrieve_by_id(Block :: class_name(), intval($blockId));

                if ($block)
                {
                    $block->setParentId($column->get_id());
                    $block->setSort($sortOrder);

                    if (! $block->update())
                    {
                        $errors ++;
                    }
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
