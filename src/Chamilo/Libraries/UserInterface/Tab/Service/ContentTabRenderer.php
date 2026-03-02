<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Trait\TabNavigatonRendererTrait;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentTabRenderer implements TabRendererInterface, TabNavigationRendererInterface
{
    use TabNavigatonRendererTrait;

    public function getTabType(): string
    {
        return ContentTab::class;
    }

    public function renderContent(ContentTab $tab, ?string $selectedTab = null): string
    {
        $isActive = $tab->getIdentifier() === $selectedTab;

        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane' . ($isActive ? ' active' : '') . ' clearfix" id="' .
            $tab->getIdentifier() . '" role="tabpanel" aria-labelledby="' . $tab->getIdentifier() .
            '-tab" tabindex="0">';
        $html[] = $tab->getContent();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}