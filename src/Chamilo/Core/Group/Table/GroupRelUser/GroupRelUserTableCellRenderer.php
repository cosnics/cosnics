<?php
namespace Chamilo\Core\Group\Table\GroupRelUser;

use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class GroupRelUserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($groupreluser)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Unsubscribe'), new FontAwesomeGlyph('times'),
                $this->get_component()->get_group_rel_user_unsubscribing_url($groupreluser), ToolbarItem::DISPLAY_ICON,
                true
            )
        );

        return $toolbar->as_html();
    }

    public function renderCell(TableColumn $column, $groupreluser): string
    {
        switch ($column->get_name())
        {
            case GroupRelUser::PROPERTY_USER_ID :
                $user_id = parent::renderCell($column, $groupreluser);
                $user = DataManager::retrieve_by_id(
                    User::class, $user_id
                );

                return $user->get_fullname();
        }

        return parent::renderCell($column, $groupreluser);
    }
}
