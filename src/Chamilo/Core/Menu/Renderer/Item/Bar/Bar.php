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

        $html[] = '<div class="chamilo-menu-item-css-icon" style="padding: 15px; padding-left: 5px;">';
        $html[] = '<span style="font-size: 20px;" class="' . $this->getItem()->getIconClass() . '"></span>';
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
        
        $classes = $this->getClasses($this->isItemSelected());
        
        $html[] = '<li class="' . implode(' ', $classes) . '">';
        $html[] = $this->getContent();
        
        $html[] = '</li>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * @param bool $isSelected
     *
     * @param array $existingClasses
     *
     * @return array
     */
    protected function getClasses($isSelected = false, $existingClasses = [])
    {
        if($isSelected)
        {
            $existingClasses[] = 'active';
        }

        return $existingClasses;
    }

    public function getContent()
    {
        return '';
    }
}
