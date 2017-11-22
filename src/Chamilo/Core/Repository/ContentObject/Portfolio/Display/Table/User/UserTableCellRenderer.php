<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\User;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 * User table cell renderer
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     * 
     * @param \core\user\storage\data_class\User $result
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('ViewAsUser', array('USER' => $result->get_fullname())), 
                Theme::getInstance()->getCommonImagePath('Action/Login'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_USER, 
                        Manager::PARAM_VIRTUAL_USER_ID => $result->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}