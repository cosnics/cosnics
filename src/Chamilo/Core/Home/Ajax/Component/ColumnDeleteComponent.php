<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class ColumnDeleteComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_COLUMN = 'column';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_COLUMN);
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
        
        $column = DataManager::retrieve_by_id(
            Column::class_name(), 
            intval($this->getPostDataValue(self::PARAM_COLUMN)));
        
        if ($column->getUserId() == $userId)
        {
            if ($column->delete())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::general_error(Translation::get('ColumnNotDeleted'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
