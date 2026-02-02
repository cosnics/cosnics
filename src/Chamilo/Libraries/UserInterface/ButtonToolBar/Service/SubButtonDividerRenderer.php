<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonDivider;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererClassesTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubButtonDividerRenderer implements ButtonRendererInterface
{
    use ButtonRendererClassesTrait;

    public function render(SubButtonDivider $subButtonDivider): string
    {
        $html = [];

        $html[] = '<li';
        $html[] = 'role="separator"';
        $html[] = 'class="' . $this->renderClasses($subButtonDivider, ['divider']) . '">';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClass(): string
    {
        return SubButtonDivider::class;
    }
}