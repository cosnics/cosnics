<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Service;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButtonHeader;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait\ButtonRendererDisplayTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubButtonHeaderRenderer implements ButtonRendererInterface, ButtonRendererDisplayInterface
{
    use ButtonRendererDisplayTrait;
    use ButtonRendererClassesTrait;

    public function render(SubButtonHeader $button): string
    {
        $html = [];

        $html[] = '<li';
        $html[] = 'class="' . $this->renderClasses($button, ['dropdown-header']) . '">';
        $html[] = 'title="' . $this->getTitle($button) . '"';
        $html[] = $this->renderInlineGlyphAndLabel($button);
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getButtonClass(): string
    {
        return SubButtonHeader::class;
    }
}