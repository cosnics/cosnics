<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererActionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererActionTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererDisplayTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererLinkTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
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

        $html[] = '<li>';

        $linkClasses = ['dropdown-item'];

        if ($button->getState()) {
            $linkClasses[] = 'active';
        }

        $html[] = $this->renderLink($button, [], $linkClasses);
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClassName(): string
    {
        return SubButton::class;
    }
}