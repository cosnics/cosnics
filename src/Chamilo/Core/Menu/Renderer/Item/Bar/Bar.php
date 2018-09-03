<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar;

use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Bar extends Renderer
{

    public function isSelected()
    {
        return $this->isItemSelected() || $this->isParentSelected();
    }

    abstract function isItemSelected();

    public function isParentSelected()
    {
        return $this->getItem()->hasParent() && $this->getParentRenderer()->isSelected();
    }

    public function canViewMenuItem(User $user)
    {
        return true;
    }

    protected function renderCssIcon()
    {
        $html = [];

        $html[] = '<div class="chamilo-menu-item-css-icon' .
            ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') . '">';
        $html[] = '<span class="chamilo-menu-item-css-icon-class ' . $this->getItem()->getIconClass() . '"></span>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function render()
    {
        if (! $this->canViewMenuItem($this->getMenuRenderer()->get_user()))
        {
            return '';
        }
        
        $html = array();
        
        $selected = $this->isItemSelected();
        
        $html[] = '<li' . ($selected ? ' class="active"' : '') . '>';
        $html[] = $this->getContent();
        
        $html[] = '</li>';
        
        return implode(PHP_EOL, $html);
    }
}
