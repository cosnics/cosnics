<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package home.ajax
 * @author Hans De Bisschop
 */
class TabEditComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_TAB = 'tab';
    const PARAM_TITLE = 'title';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_TAB, self::PARAM_TITLE);
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
        
        $tab = intval($this->getPostDataValue(self::PARAM_TAB));
        $title = $this->getPostDataValue(self::PARAM_TITLE);
        
        $tab = DataManager::retrieve_by_id(Tab::class_name(), $tab);
        
        if ($tab->getUserId() == $userId)
        {
            $tab->setTitle($title);
            
            if ($tab->update())
            {
                JsonAjaxResult::success();
            }
            else
            {
                JsonAjaxResult::general_error(Translation::get('TabNotUpdated'));
            }
        }
        else
        {
            JsonAjaxResult::not_allowed();
        }
    }
}
