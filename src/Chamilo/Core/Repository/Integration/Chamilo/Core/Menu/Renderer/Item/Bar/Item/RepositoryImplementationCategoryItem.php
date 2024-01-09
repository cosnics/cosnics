<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Item\CategoryItem;
use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryImplementationCategoryItem extends CategoryItem
{

    public function isItemSelected()
    {
        // TODO;
        return false;
    }

    public function render()
    {
        if (! $this->canViewMenuItem($this->getMenuRenderer()->get_user()))
        {
            return '';
        }
        
        $html = array();
        $sub_html = array();
        $instances = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieves(
            Instance::class_name(), 
            new DataClassRetrievesParameters());
        
        if ($instances->size())
        {
            $sub_html[] = '<ul class="dropdown-menu">';
            
            while ($instance = $instances->next_result())
            {
                if (! $instance->is_enabled())
                {
                    continue;
                }
                
                $sysImagePath = Theme::getInstance()->getImagePath(
                    $instance->get_implementation(), 
                    'Menu', 
                    'png', 
                    false);
                
                $display = file_exists($sysImagePath) ? Item::DISPLAY_ICON : Item::DISPLAY_TEXT;
                $display = Item::DISPLAY_TEXT;
                
                $instanceItem = new \Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationItem();
                $instanceItem->set_implementation($instance->get_implementation());
                $instanceItem->set_instance_id($instance->get_id());
                $instanceItem->set_name($instance->get_title());
                $instanceItem->set_parent($this->getItem()->get_id());
                $instanceItem->set_display($display);
                
                $sub_html[] = Renderer::toHtml($this->getMenuRenderer(), $instanceItem, $this);
            }
            
            $sub_html[] = '</ul>';
        }
        
        $selected = $this->isItemSelected();
        
        $html[] = '<li class="nav-item dropdown' . ($selected ? ' active' : '') . '">';
        $html[] = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';
        
        $title = Translation::get('Instance', null, 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu');
        
        if ($this->getItem()->show_icon())
        {
            $integrationNamespace = 'Chamilo\Core\Repository\Integration\Chamilo\Core\Menu';
            $imagePath = Theme::getInstance()->getImagePath(
                $integrationNamespace, 
                'RepositoryImplementationCategory' . ($selected ? 'Selected' : ''));
            
            $html[] = '<img class="chamilo-menu-item-icon' .
                 ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') . '" src="' . $imagePath .
                 '" title="' . $title . '" alt="' . $title . '" />';
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
