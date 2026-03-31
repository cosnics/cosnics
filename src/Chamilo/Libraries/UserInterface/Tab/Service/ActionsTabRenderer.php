<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ActionsTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabContentInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabContentRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Trait\TabNavigatonRendererTrait;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionsTabRenderer implements TabRendererInterface, TabNavigationRendererInterface, TabContentRendererInterface
{
    use TabNavigatonRendererTrait;

    public function __construct(protected ActionRenderer $actionRenderer)
    {
    }

    public function getTabType(): string
    {
        return ActionsTab::class;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ActionsTab $tab
     */
    public function renderContent(TabContentInterface $tab, ?string $selectedTab = null): string
    {
        $isActive = $tab->getIdentifier() === $selectedTab;

        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane' . ($isActive ? ' active' : '') . ' clearfix" id="' .
            $tab->getIdentifier() . '" role="tabpanel" aria-labelledby="' . $tab->getIdentifier() .
            '-tab" tabindex="0">';
        $html[] = '<ul class="list-group">';

        foreach ($tab->getActions() as $action) {
            $html[] = $this->actionRenderer->render($action);
        }

        $html[] = '</ul>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}