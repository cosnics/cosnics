<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonRendererDisplayTrait
{
    public function getTitle(ButtonDisplayInterface $button): ?string
    {
        return htmlentities(htmlspecialchars(strip_tags($button->getLabel())));
    }

    public function renderInlineGlyphAndLabel(ButtonDisplayInterface $button): string
    {
        $html = [];

        $displayLabel = $button->getDisplay() != ButtonDisplayInterface::DISPLAY_ICON && $button->getLabel();
        $displayIcon = $button->getDisplay() != ButtonDisplayInterface::DISPLAY_LABEL && $button->getInlineGlyph();

        if ($displayIcon) {
            $html[] = $button->getInlineGlyph()->render();
        }

        if ($displayLabel) {
            $html[] = '<span>' . $button->getLabel() . '</span> ';
        }

        return implode('', $html);
    }
}