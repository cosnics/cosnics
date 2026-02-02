<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonActionInterface;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererLinkTrait
{
    use ButtonRendererClassesTrait;
    use ButtonRendererActionTrait;
    use ButtonRendererDisplayTrait;

    public function renderLink(
        ButtonActionInterface&ButtonDisplayInterface $button, array $baseClassesBefore = [],
        array $baseClassesAfter = []
    ): string
    {
        if (!$button->getAction()) {
            $baseClassesAfter[] = 'disabled';
        }

        $html = [];

        $html[] = '<a';
        $html[] = 'class="' . $this->renderClasses($button, $baseClassesBefore, $baseClassesAfter) . '"';
        $html[] = 'title="' . $this->getTitle($button) . '"';
        $html[] = $this->renderAction($button);
        $html[] = '>';
        $html[] = $this->renderInlineGlyphAndLabel($button);
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}