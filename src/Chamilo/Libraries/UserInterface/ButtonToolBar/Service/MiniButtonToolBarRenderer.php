<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\MiniButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererClassesTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniButtonToolBarRenderer extends AbstractButtonCollectionButtonRenderer implements ButtonRendererInterface
{
    use ButtonRendererClassesTrait;

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     */
    public function render(MiniButtonToolBar $miniButtonToolBar): string
    {
        $html = [];

        $html[] = '<div';
        $html[] = 'class="' . $this->renderClasses($miniButtonToolBar, ['btn-toolbar', 'btn-toolbar-cosnics']) . '">';
        $html[] = '<div class="btn-group ">';

        foreach ($miniButtonToolBar->getButtons() as $button) {
            $html[] = $this->getButtonRendererCollection()->getButtonRendererForButton($button)->render($button);
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClassName(): string
    {
        return MiniButtonToolBar::class;
    }
}