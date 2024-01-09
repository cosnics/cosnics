<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Item\CategoryItem;
use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\EntityService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceCategoryItem extends CategoryItem
{

    public function isItemSelected()
    {
        $currentContext = $this->getMenuRenderer()->getRequest()->get(Application::PARAM_CONTEXT);
        if ($currentContext == Manager::package())
        {
            return true;
        }
        
        $currentWorkspace = $this->getMenuRenderer()->getRequest()->get(
            \Chamilo\Core\Repository\Manager::PARAM_WORKSPACE_ID);
        
        return isset($currentWorkspace);
    }

    public function render()
    {
        if (! $this->canViewMenuItem($this->getMenuRenderer()->get_user()))
        {
            return '';
        }
        
        $html = array();
        $sub_html = array();
        
        $workspaceService = new WorkspaceService(new WorkspaceRepository());
        $workspaces = $workspaceService->getWorkspaceFavouritesByUserFast(
            $this->getMenuRenderer()->get_user()
        );
        
        $sub_html[] = '<ul class="dropdown-menu">';
        
        if ($workspaces->size())
        {
            while ($workspace = $workspaces->next_result())
            {
                $workspaceItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceItem();
                $workspaceItem->setWorkspaceId($workspace->getId());
                $workspaceItem->setName($workspace->getName());
                $workspaceItem->set_parent($this->getItem()->get_id());
                $workspaceItem->set_display($this->getItem()->get_display());
                
                $sub_html[] = Renderer::toHtml($this->getMenuRenderer(), $workspaceItem, $this);
            }
        }
        
        $configurationItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceConfigureItem();
        $configurationItem->set_parent($this->getItem()->get_id());
        $configurationItem->set_display($this->getItem()->get_display());
        
        $sub_html[] = Renderer::toHtml($this->getMenuRenderer(), $configurationItem, $this);
        
        $sub_html[] = '</ul>';
        
        $selected = $this->isSelected();
        
        $html[] = '<li class="nav-item dropdown' . ($selected ? ' active' : '') . '">';
        $html[] = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
        
        $title = Translation::get('Workspaces');
        
        if ($this->getItem()->show_icon())
        {
            $integrationNamespace = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';
            $imagePath = Theme::getInstance()->getImagePath(
                $integrationNamespace, 
                'WorkspaceCategory' . ($selected ? 'Selected' : ''));
            
            $html[] = '<img class="chamilo-menu-item-icon' .
                 ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $imagePath . '" title="' . $title .
                 '" alt="' . $title . '" />';
        }
        
        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                 ($this->getItem()->show_icon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }
        
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';
        
        $html[] = implode(PHP_EOL, $sub_html);
        
        $html[] = '</li>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns whether or not the given user can view this menu item
     * 
     * @param User $user
     *
     * @return bool
     */
    public function canViewMenuItem(User $user)
    {
        $authorizationChecker = $this->getAuthorizationChecker();
        return $authorizationChecker->isAuthorized($this->getMenuRenderer()->get_user(), 'Chamilo\Core\Repository');
    }
}
