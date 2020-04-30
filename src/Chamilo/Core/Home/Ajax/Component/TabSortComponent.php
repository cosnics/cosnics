<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class TabSortComponent extends Manager
{
    const PARAM_ORDER = 'order';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_ORDER);
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
        
        parse_str($this->getPostDataValue(self::PARAM_ORDER), $tabs);
        
        $errors = 0;
        
        foreach ($tabs[self::PARAM_ORDER] as $sortOrder => $tabId)
        {
            $tab = DataManager::retrieve_by_id(Tab::class, intval($tabId));
            $tab->setSort($sortOrder + 1);
            
            if (! $tab->update())
            {
                $errors ++;
            }
        }
        
        if ($errors > 0)
        {
            JsonAjaxResult::error(409, Translation::get('OneOrMoreTabsNotUpdated'));
        }
        else
        {
            JsonAjaxResult::success();
        }
    }
}
