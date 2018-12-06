<?php

namespace Chamilo\Core\Menu\Renderer\Item\Bar;

/**
 * @package Chamilo\Core\Menu\Renderer\Item\Bar
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class PriorityItem extends Bar
{
    /**
     * @param bool $isSelected
     *
     * @param array $existingClasses
     *
     * @return array
     */
    protected function getClasses($isSelected = false, $existingClasses = [])
    {
        $existingClasses[] = 'chamilo-menu-item-priority';
        return parent::getClasses($isSelected, $existingClasses);
    }
}