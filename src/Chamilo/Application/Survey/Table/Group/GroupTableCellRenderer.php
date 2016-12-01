<?php
namespace Chamilo\Application\Survey\Table\Group;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class GroupTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($group)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('CancelInvitation'), 
                Theme::getInstance()->getCommonImagePath('Action/Unsubscribe'), 
                $this->get_component()->get_survey_cancel_invitation_url(
                    $this->get_component()->get_publication_id(), 
                    $group->get_id()), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>