<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton;
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
class SubButtonRenderer
    implements ButtonRendererInterface, ButtonRendererDisplayInterface, ButtonRendererActionInterface
{
    use ButtonRendererDisplayTrait;
    use ButtonRendererActionTrait;
    use ButtonRendererLinkTrait;

    public function render(SubButton $button): string
    {
        $html = [];

        $html[] = '<li' . ($button->getState() ? ' class="active"' : '') . '>';
        $html[] = $this->renderLink($button);
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClass(): string
    {
        return SubButton::class;
    }
}