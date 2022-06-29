<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class ColumnWidthComponent extends Manager
{
    const PARAM_COLUMN = 'column';
    const PARAM_WIDTH = 'width';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_COLUMN, self::PARAM_WIDTH);
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
        $columnWidth = $this->getPostDataValue(self::PARAM_WIDTH);
        
        $column = DataManager::retrieve_by_id(Column::class, $columnId);
        
        if ($column->getUserId() == $userId)
        {
            $column->setWidth((int) $columnWidth);
            if ($column->update())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::error(409, Translation::get('ColumnNotUpdated'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
