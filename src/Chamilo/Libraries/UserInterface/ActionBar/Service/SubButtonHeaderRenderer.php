<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButtonHeader;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonRendererInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererClassesTrait;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Trait\ButtonRendererDisplayTrait;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
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