<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Hans De Bisschop @dependency repository.content_object.assessment_multiple_choice_question;
 */
class TabSortComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PARAM_ORDER = 'order';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_ORDER);
    }

    public function get_tabs()
    {
        $element_data = explode('&', $this->getPostDataValue(self :: PARAM_ORDER));
        $elements = array();
        
        foreach ($element_data as $element)
        {
            $element_split = explode('=', $element);
            $elements[] = $element_split[1];
        }
        
        return $elements;
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
        
        $tabs = $this->get_tabs();
        
        $errors = 0;
        $i = 1;
        
        foreach ($tabs as $tab_id)
        {
            $tab = DataManager :: retrieve_by_id(Tab :: class_name(), intval($tab_id));
            $tab->set_sort($i);
            if (! $tab->update())
            {
                $errors ++;
            }
            $i ++;
        }
        
        if ($errors > 0)
        {
            JsonAjaxResult :: error(409, Translation :: get('OneOrMoreTabsNotUpdated'));
        }
        else
        {
            JsonAjaxResult :: success();
        }
    }
}
