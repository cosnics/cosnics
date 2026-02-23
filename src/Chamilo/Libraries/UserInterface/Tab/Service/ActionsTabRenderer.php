<?php
namespace Chamilo\Libraries\UserInterface\Tab\Service;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ActionsTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabNavigationRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabRendererInterface;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Trait\TabNavigatonRendererTrait;

/**
 * @package Chamilo\Libraries\UserInterface\Tab\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionsTabRenderer implements TabRendererInterface, TabNavigationRendererInterface
{
    use TabNavigatonRendererTrait;

    private ActionRenderer $actionRenderer;

    public function __construct(ActionRenderer $actionRenderer)
    {
        $this->actionRenderer = $actionRenderer;
    }

    public function getActionRenderer(): ActionRenderer
    {
        return $this->actionRenderer;
    }

    public function renderContent(ActionsTab $tab): string
    {
        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane clearfix" id="' . $tab->getIdentifier() .
            '" role="tabpanel" aria-labelledby="' . $tab->getIdentifier() . '-tab" tabindex="0">';
        $html[] = '<div class="list-group">';

        foreach ($tab->getActions() as $action) {
            $html[] = $this->getActionRenderer()->render($action);
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}