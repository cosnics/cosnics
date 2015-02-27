<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class GroupsViewerComponent extends Manager
{

    private $bitbucket;

    public function run()
    {
        $html = array();

        $html[] = $this->render_header();

        $html[] = $this->get_action_bar()->as_html();

        $groups = $this->get_external_repository_manager_connector()->retrieve_groups(
            Setting :: get('username', $this->get_external_repository()->get_id()));

        if ($groups)
        {
            $list_groups = array();
            foreach ($groups as $group)
            {
                $group_row = array();
                $group_row[] = $group->get_name();
                $group_row[] = $group->get_permission();

                $member_row = array();
                foreach ($group->get_members() as $member)
                {
                    $member_row[] = '<option>';
                    if (! $member->first_name || ! $member->last_name)
                    {
                        $member_row[] = $member->username;
                    }
                    else
                    {
                        $member_row[] = $member->first_name;
                        $member_row[] = ' ' . $member->last_name;
                        $member_row[] = ' (' . $member->username . ')';
                    }
                    $member_row[] = '</option>';
                }

                $group_row[] = '<select>' . implode('', $member_row) . '</select>';
                $actions = array();

                $toolbar_item = new ToolbarItem(
                    Translation :: get('Delete'),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png',
                    $this->get_external_repository_group_deleting_url($group->get_id()),
                    ToolbarItem :: DISPLAY_ICON);
                $actions[] = $toolbar_item->as_html();
                $toolbar_item = new ToolbarItem(
                    Translation :: get('AddUser'),
                    Theme :: getInstance()->getImagesPath() . 'action_add_user.png',
                    $this->get_external_repository_adding_user_url($group->get_id()),
                    ToolbarItem :: DISPLAY_ICON);
                $actions[] = $toolbar_item->as_html();
                $toolbar_item = new ToolbarItem(
                    Translation :: get('DeleteUser'),
                    Theme :: getInstance()->getImagesPath() . 'action_delete_user.png',
                    $this->get_external_repository_deleting_user_url($group->get_id()),
                    ToolbarItem :: DISPLAY_ICON);
                $actions[] = $toolbar_item->as_html();
                $group_row[] = implode(' ', $actions);
                $list_groups[] = $group_row;
            }

            $table = new SortableTableFromArray($list_groups);
            $table->set_header(0, Translation :: get('Name'));
            $table->set_header(1, Translation :: get('Permission'));
            $table->set_header(2, Translation :: get('Members'));
            $table->set_header(3, '');

            $html[] = $table->as_html();
        }
        else
        {
            $html[] = $this->display_warning_message(Translation :: get('NoGroups'));
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('CreateGroup'),
                Theme :: getInstance()->getImagesPath() . 'action_create.png',
                $this->get_external_repository_group_creating_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
