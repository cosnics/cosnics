<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
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
        $tab->setTitle(Translation :: get('NewTab'));
        $tab->setUserId($user_id);
        if (! $tab->create())
        {
            JsonAjaxResult :: general_error(Translation :: get('TabNotAdded'));
        }
        
        $column = new Column();
        $column->setParentId($tab->get_id());
        $column->setTitle(Translation :: get('NewColumn'));
        $column->setSort('1');
        $column->setWidth(100);
        $column->setUserId($user_id);
        if (! $column->create())
        {
            JsonAjaxResult :: general_error(Translation :: get('TabColumnNotAdded'));
        }
        
        $html[] = '<div class="portal_tab" id="portal_tab_' . $tab->getId() . '" style="display: none;">';
        $html[] = '<div class="portal_column" id="portal_column_' . $column->getId() . '" style="width: ' .
             $column->getWidth() . '%;">';
        
        $html[] = '<div class="empty_portal_column" style="display:block;">';
        $html[] = htmlspecialchars(Translation :: get('EmptyColumnText'));
        $img = Theme :: getInstance()->getImagePath('Chamilo\Core\Home', 'Action/RemoveColumn');
        $html[] = '<div class="deleteColumn"><a href="#"><img src="' . $img . '" alt="' .
             Translation :: get('RemoveColumn') . '"/></a></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        $html[] = '</div>';
        $html[] = '</div>';
        
        $title = array();
        $title[] = '<li class="normal" id="tab_select_' . $tab->getId() . '">';
        $title[] = '<a class="tabTitle" href="#">' . $tab->getTitle() . '</a>';
        $title[] = '<a class="deleteTab"><img src="' . Theme :: getInstance()->getImagePath(
            'Chamilo\Core\Home', 
            'Action/DeleteTab') . '" /></a>';
        $title[] = '</li>';
        
        $result = new JsonAjaxResult(200);
        $result->set_property(self :: PROPERTY_HTML, implode(PHP_EOL, $html));
        $result->set_property(self :: PROPERTY_TITLE, implode(PHP_EOL, $title));
        $result->display();
    }
}
