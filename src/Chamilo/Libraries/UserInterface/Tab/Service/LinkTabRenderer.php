<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabRendererInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTabRenderer implements TabRendererInterface, TabNavigationRendererInterface
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getTabType(): string
    {
        return LinkTab::class;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab $tab
     */
    public function renderNavigation(TabNavigationInterface $tab, ?string $selectedTab = null): string
    {
        $isActive = $tab->getIdentifier() === $selectedTab;

        $html = [];

        $html[] = '<li class="nav-item" role="presentation">';
        $html[] = '<a href="' . $tab->getLink() . '" class="nav-link' . ($isActive ? ' active' : '') . '" id="' .
            $tab->getIdentifier() . '-tab">';

        if ($tab->getInlineGlyph() && $tab->isIconVisible()) {
            $html[] = $tab->getInlineGlyph()->render();
        }

        if ($tab->getLabel() && $tab->isTextVisible()) {
            $html[] = '<span class="title">' . $tab->getLabel() . '</span>';
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }
}
