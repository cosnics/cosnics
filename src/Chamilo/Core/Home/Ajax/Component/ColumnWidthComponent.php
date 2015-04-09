<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class ColumnWidthComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_COLUMN = 'column';
    const PARAM_WIDTH = 'width';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_COLUMN, self :: PARAM_WIDTH);
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
        $column_width = $this->getPostDataValue(self :: PARAM_WIDTH);
        $column_width = str_replace('%', '', $column_width);
        $column_width = min(100, $column_width);
        $column_width = $column_width . '%';
        
        $column = DataManager :: retrieve_by_id(Column :: class_name(), intval($column_data[2]));
        
        if ($column->get_user() == $user_id)
        {
            $column->set_width($column_width);
            if ($column->update())
            {
                JsonAjaxResult :: success();
            }
            else
            {
                JsonAjaxResult :: error(409, Translation :: get('ColumnNotUpdated'));
            }
        }
        else
        {
            JsonAjaxResult :: not_allowed();
        }
    }
}
