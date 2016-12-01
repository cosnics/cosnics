<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class ViewerComponent extends Manager
{

    function get_tool_actions()
    {
        $tool_actions = array();
        
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $tool_actions[] = new ToolbarItem(
                Translation::get('Test'), 
                Theme::getInstance()->getCommonImagePath('Action/Import'), 
                '#', 
                ToolbarItem::DISPLAY_ICON_AND_LABEL);
        }
        
        /*
         * if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT)) { $action_name = Translation ::
         * get('ViewResultsSummary'); } else { $action_name = Translation :: get('ViewResults'); } $tool_actions[] = new
         * ToolbarItem( $action_name, Theme :: getInstance()->getCommonImagePath('action_view_results'), '#',
         * ToolbarItem :: DISPLAY_ICON_AND_LABEL );
         */
        
        return $tool_actions;
    }
}