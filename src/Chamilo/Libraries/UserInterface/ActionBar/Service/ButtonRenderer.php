<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererActionInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererActionTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererDisplayTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererLinkTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonRenderer implements ButtonRendererInterface, ButtonRendererDisplayInterface, ButtonRendererActionInterface
{
    use ButtonRendererDisplayTrait;
    use ButtonRendererActionTrait;
    use ButtonRendererLinkTrait;

    public function render(Button $button): string
    {
        return $this->renderLink($button, ['btn', 'btn-default']);
    }

    public function getButtonClass(): string
    {
        return Button::class;
    }
}