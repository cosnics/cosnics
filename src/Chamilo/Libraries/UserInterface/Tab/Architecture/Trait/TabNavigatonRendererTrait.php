<?php
namespace Chamilo\Libraries\UserInterface\Tab\Architecture\Trait;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationInterface;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait TabNavigatonRendererTrait
{
    public function renderNavigation(TabNavigationInterface $tab, ?string $selectedTab = null): string
    {
        $isActive = $tab->getIdentifier() === $selectedTab;

        $html = [];

        $html[] = '<li class="nav-item" role="presentation">';
        $html[] = '<button class="nav-link' . ($isActive ? ' active' : '') . '" id="' . $tab->getIdentifier() .
            '-tab" data-bs-toggle="tab" data-bs-target="#' . $tab->getIdentifier() .
            '" type="button" role="tab" aria-controls="' . $tab->getIdentifier() . '">';

        if ($tab->getInlineGlyph() && $tab->isIconVisible()) {
            $html[] = $tab->getInlineGlyph()->render();
        }

        if ($tab->getLabel() && $tab->isTextVisible()) {
            $html[] = '<span class="title">' . $tab->getLabel() . '</span>';
        }

        $html[] = '</button>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}