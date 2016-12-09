<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\Component;

use Chamilo\Core\Repository\Implementation\Bitbucket\Form\PriviligeForm;
use Chamilo\Core\Repository\Implementation\Bitbucket\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class PrivilegesViewerComponent extends Manager
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $repository;

    public function run()
    {
        $id = Request::get(Manager::PARAM_EXTERNAL_REPOSITORY_ID);
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        if ($id)
        {
            $this->repository = $this->retrieve_external_repository_object($id);
            
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $id;
            $privilege_form = new PriviligeForm($this->get_url($parameters), $this);
            
            if ($privilege_form->validate())
            {
                $success = $privilege_form->grant_privilege();
                $message = $success ? Translation::get('GrantPrivilegeCreated') : Translation::get(
                    'GrantPrivilegeNotCreated');
                
                $this->redirect($message, ! $success, $parameters);
            }
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = '<h3>' . $this->repository->get_title() . '</h3>';
            $html[] = $this->getButtonToolbarRenderer($id)->render();
            $html[] = $privilege_form->toHtml();
            
            $privileges = $this->repository->get_privileges();
            $privileges_groups = $this->repository->get_groups_privileges();
            
            if (count($privileges) > 0 || count($privileges_groups) > 0)
            {
                if (count($privileges) > 0)
                {
                    $data = array();
                    
                    foreach ($privileges as $privilege)
                    {
                        $toolbar_item = new ToolbarItem(
                            Translation::get('Delete'), 
                            Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                            $this->get_external_repository_privilege_revoking_url($id, $privilege->get_username()), 
                            ToolbarItem::DISPLAY_ICON);
                        
                        $row = array();
                        
                        $row[] = $privilege->get_username();
                        $row[] = $privilege->get_privilege();
                        $row[] = $privilege->get_first_name();
                        $row[] = $privilege->get_last_name();
                        $row[] = $toolbar_item->as_html();
                        
                        $data[] = $row;
                    }
                    
                    $headers = array();
                    $headers[] = new SortableStaticTableColumn(Translation::get('Username'));
                    $headers[] = new SortableStaticTableColumn(Translation::get('Privilege'));
                    $headers[] = new SortableStaticTableColumn(Translation::get('FirstName'));
                    $headers[] = new SortableStaticTableColumn(Translation::get('LastName'));
                    $headers[] = new SortableStaticTableColumn('');
                    
                    $table = new SortableTableFromArray($data, $headers);
                    
                    $html[] = $table->toHtml();
                }
                
                if (count($privileges_groups) > 0)
                {
                    $data = array();
                    
                    foreach ($privileges_groups as $privilege)
                    {
                        $toolbar_item = new ToolbarItem(
                            Translation::get('Delete'), 
                            Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                            $this->get_external_repository_group_privilege_revoking_url(
                                $id, 
                                $privilege->get_owner_username() . '/' . $privilege->get_group()), 
                            ToolbarItem::DISPLAY_ICON);
                        
                        $row = array();
                        
                        $row[] = $privilege->get_name();
                        $row[] = $privilege->get_privilege();
                        $row[] = $toolbar_item->as_html();
                        
                        $data[] = $row;
                    }
                    
                    $headers = array();
                    $headers[] = new SortableStaticTableColumn(Translation::get('Group'));
                    $headers[] = new SortableStaticTableColumn(Translation::get('Privilege'));
                    $headers[] = new SortableStaticTableColumn('');
                    
                    $table = new SortableTableFromArray($data, $headers);
                    
                    $html[] = $table->toHtml();
                }
            }
            else
            {
                $html[] = $this->display_warning_message(Translation::get('NoPrivileges'));
            }
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function get_repository()
    {
        return $this->repository;
    }

    public function getButtonToolbarRenderer($id)
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $commonActions->addButton(
                new Button(
                    Translation::get('RevokeAll'), 
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\Implementation\Bitbucket', 
                        'Action/Revoke'), 
                    $this->get_external_repository_privilege_revoking_url($id), 
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }
}
