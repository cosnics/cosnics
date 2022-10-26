<?php
namespace Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Core\Menu\Renderer\ItemRenderer;

/**
 * @package Chamilo\Core\Menu\Renderer\Item\Bar
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PriorityItemRenderer extends ItemRenderer
{
    /**
     * @param bool $isSelected
     * @param string[] $existingClasses
     *
     * @return string[]
     */
    protected function getClasses($isSelected = false, $existingClasses = [])
    {
        $existingClasses[] = 'chamilo-menu-item-priority';

        return parent::getClasses($isSelected, $existingClasses);
    }
}