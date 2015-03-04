<?php
namespace Chamilo\Core\Group\Table\GroupRelUser;

use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class GroupRelUserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $groupreluser)
    {
        switch ($column->get_name())
        {
            case GroupRelUser :: PROPERTY_USER_ID :
                $user_id = parent :: render_cell($column, $groupreluser);
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(), 
                    $user_id);
                return $user->get_fullname();
        }
        return parent :: render_cell($column, $groupreluser);
    }

    public function get_actions($groupreluser)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Unsubscribe'), 
                Theme :: getInstance()->getCommonImagePath('action_delete'), 
                $this->get_component()->get_group_rel_user_unsubscribing_url($groupreluser), 
                ToolbarItem :: DISPLAY_ICON, 
                true));
        
        return $toolbar->as_html();
    }
}
