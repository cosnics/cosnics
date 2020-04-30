<?php
namespace Chamilo\Core\Group\Table\Group;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class GroupTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function get_actions($group)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->get_component()->get_group_editing_url($group), ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('AddUsers'), new FontAwesomeGlyph('plus-circle'),
                $this->get_component()->get_group_suscribe_user_browser_url($group), ToolbarItem::DISPLAY_ICON
            )
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group->get_id())
        );
        $users = $this->get_component()->retrieve_group_rel_users($condition);
        $visible = ($users->size() > 0);

        if ($visible)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Truncate'), new FontAwesomeGlyph('trash-alt'),
                    $this->get_component()->get_group_emptying_url($group), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }
        else
        {

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('TruncateNA'), new FontAwesomeGlyph('trash-alt', array('text-muted')), null,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                $this->get_component()->get_group_delete_url($group), ToolbarItem::DISPLAY_ICON, true
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Move', null, Utilities::COMMON_LIBRARIES),
                new FontAwesomeGlyph('window-restore', array('fa-flip-horizontal'), null, 'fas'),
                $this->get_component()->get_move_group_url($group), ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Metadata'), new FontAwesomeGlyph('info-circle'),
                $this->get_component()->get_group_metadata_url($group), ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->as_html();
    }

    public function render_cell($column, $group)
    {
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case Group::PROPERTY_NAME :
                $title = parent::render_cell($column, $group);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }

                return '<a href="' . htmlentities($this->get_component()->get_group_viewing_url($group)) . '" title="' .
                    $title . '">' . $title_short . '</a>';
            case Group::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::render_cell($column, $group));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }

                return StringUtilities::getInstance()->truncate($description);
            case Translation::get(GroupTableColumnModel::USERS, null, Manager::context()) :
                return $group->count_users();
            case Translation::get(GroupTableColumnModel::SUBGROUPS) :
                return $group->count_subgroups(true, true);
        }

        return parent::render_cell($column, $group);
    }
}
