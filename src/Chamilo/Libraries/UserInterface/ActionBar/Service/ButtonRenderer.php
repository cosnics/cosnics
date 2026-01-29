<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;

/**
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonRenderer
{
    public function render(Button $button): string
    {
        $html = [];

        $html[] = $this->renderLinkOpeningTag($button);
        $html[] = $this->renderLinkContent($button);
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function determineClasses(Button $button): array
    {
        $classes = array_merge(['btn', 'btn-default'], $button->getClasses());

        if (!$button->getAction())
        {
            $classes[] = 'disabled';
        }

        return $classes;
    }

    public function getTitle(Button $button): ?string
    {
        return htmlspecialchars(strip_tags($button->getLabel()));
    }

    public function renderAction(Button $button): string
    {
        $html = [];

        if ($button->getAction())
        {
            $html[] = 'href="' . htmlentities($button->getAction()) . '"';

            if ($button->getTarget())
            {
                $html[] = 'target="' . $button->getTarget() . '"';
            }

            if ($button->needsConfirmation())
            {
                $html[] = 'onclick="return confirm(\'' . addslashes(htmlentities($button->getConfirmationMessage())) .
                    '\');"';
            }
        }

        return implode(' ', $html);
    }

    public function renderClasses(Button $button): string
    {
        return 'class="' . implode(' ', $this->determineClasses($button)) . '"';
    }

    public function renderLinkContent(Button $button): string
    {
        $html = [];

        $displayLabel = $button->getDisplay() != ButtonDisplayInterface::DISPLAY_ICON && $button->getLabel();
        $displayIcon = $button->getDisplay() != ButtonDisplayInterface::DISPLAY_LABEL && $button->getInlineGlyph();

        if ($displayIcon)
        {
            $html[] = $button->getInlineGlyph()->render();
        }

        if ($displayLabel)
        {
            $html[] = '<span>' . $button->getLabel() . '</span> ';
        }

        return implode('', $html);
    }

    public function renderLinkOpeningTag(Button $button): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = $this->renderClasses($button);
        $html[] = 'title="' . htmlentities($this->getTitle($button)) . '"';
        $html[] = $this->renderAction($button);
        $html[] = '>';

        return implode(' ', $html);
    }
}