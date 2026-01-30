<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonDivider;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererClassesTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
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