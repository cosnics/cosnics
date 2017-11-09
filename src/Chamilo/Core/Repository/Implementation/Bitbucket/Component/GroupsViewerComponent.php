<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

class GroupsViewerComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $bitbucket;

    public function run()
    {
        $html = array();
        
        $html[] = $this->render_header();
        
        $html[] = $this->buttonToolbarRenderer->render();
        
        $groups = $this->get_external_repository_manager_connector()->retrieve_groups(
            Setting::get('username', $this->get_external_repository()->get_id()));
        
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
                    Translation::get('Delete'), 
                    Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                    $this->get_external_repository_group_deleting_url($group->get_id()), 
                    ToolbarItem::DISPLAY_ICON);
                $actions[] = $toolbar_item->as_html();
                $toolbar_item = new ToolbarItem(
                    Translation::get('AddUser'), 
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\Implementation\Bitbucket', 
                        'Action/AddUser'), 
                    $this->get_external_repository_adding_user_url($group->get_id()), 
                    ToolbarItem::DISPLAY_ICON);
                $actions[] = $toolbar_item->as_html();
                $toolbar_item = new ToolbarItem(
                    Translation::get('DeleteUser'), 
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\Implementation\Bitbucket', 
                        'Action/DeleteUser'), 
                    $this->get_external_repository_deleting_user_url($group->get_id()), 
                    ToolbarItem::DISPLAY_ICON);
                $actions[] = $toolbar_item->as_html();
                $group_row[] = implode(' ', $actions);
                $list_groups[] = $group_row;
            }
            
            $headers = array();
            $headers[] = new SortableStaticTableColumn(Translation::get('Name'));
            $headers[] = new SortableStaticTableColumn(Translation::get('Permission'));
            $headers[] = new SortableStaticTableColumn(Translation::get('Members'));
            $headers[] = new SortableStaticTableColumn('');
            
            $table = new SortableTableFromArray($list_groups, $headers);
            
            $html[] = $table->toHtml();
        }
        else
        {
            $html[] = $this->display_warning_message(Translation::get('NoGroups'));
        }
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            
            $commonActions->addButton(
                new Button(
                    Translation::get('CreateGroup'), 
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\Implementation\Bitbucket', 
                        'Action/Create'), 
                    $this->get_external_repository_group_creating_url(), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }
}
