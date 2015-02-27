<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Row;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @author Hans De Bisschop
 */
class TabAddComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PROPERTY_HTML = 'html';
    const PROPERTY_TITLE = 'title';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array();
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
        
        $tab = new Tab();
        $tab->set_title(Translation :: get('NewTab'));
        $tab->set_user($user_id);
        if (! $tab->create())
        {
            JsonAjaxResult :: general_error(Translation :: get('TabNotAdded'));
        }
        
        $row = new Row();
        $row->set_title(Translation :: get('NewRow'));
        $row->set_tab($tab->get_id());
        $row->set_user($user_id);
        if (! $row->create())
        {
            JsonAjaxResult :: general_error(Translation :: get('TabRowNotAdded'));
        }
        
        $column = new Column();
        $column->set_row($row->get_id());
        $column->set_title(Translation :: get('NewColumn'));
        $column->set_sort('1');
        $column->set_width('100');
        $column->set_user($user_id);
        if (! $column->create())
        {
            JsonAjaxResult :: general_error(Translation :: get('TabColumnNotAdded'));
        }
        
        $html[] = '<div class="portal_tab" id="portal_tab_' . $tab->get_id() . '" style="display: none;">';
        $html[] = '<div class="portal_row" id="portal_row_' . $row->get_id() . '">';
        $html[] = '<div class="portal_column" id="portal_column_' . $column->get_id() . '" style="width: ' .
             $column->get_width() . '%;">';
        
        // $html[] = $block_component->as_html();
        
        $html[] = '<div class="empty_portal_column" style="display:block;">';
        $html[] = htmlspecialchars(Translation :: get('EmptyColumnText'));
        $img = Theme :: getInstance()->getImagePath(__NAMESPACE__) . 'action_remove_column.png';
        $html[] = '<div class="deleteColumn"><a href="#"><img src="' . $img . '" alt="' .
             Translation :: get('RemoveColumn') . '"/></a></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $title = array();
        $title[] = '<li class="normal" id="tab_select_' . $tab->get_id() . '">';
        $title[] = '<a class="tabTitle" href="#">' . $tab->get_title() . '</a>';
        $title[] = '<a class="deleteTab"><img src="' . Theme :: getInstance()->getImagePath() .
             'action_delete_tab.png" /></a>';
        $title[] = '</li>';
        
        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PROPERTY_HTML, implode(PHP_EOL, $html));
        $result->set_property(self :: PROPERTY_TITLE, implode(PHP_EOL, $title));
        $result->display();
    }
}
