<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SubButtonRenderer
{
    public function render(SubButton $button): string
    {
        $html = [];

        $html[] = '<li' . ($button->getState() ? ' class="active"' : '') . '>';
        $html[] = $this->renderLink($button);
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function determineClasses(SubButton $button): array
    {
        $classes = [];

        if (!$button->getAction())
        {
            $classes[] = 'disabled';
        }

        return array_merge($button->getClasses(), $classes);
    }

    public function getLabel(SubButton $button): ?string
    {
        return ($button->getLabel() ?: null);
    }

    public function getTitle(SubButton $button): ?string
    {
        return htmlspecialchars(strip_tags($this->getLabel($button)));
    }

    public function renderAction(SubButton $button): string
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

    public function renderLink(SubButton $button): string
    {
        $html = [];

        $html[] = $this->renderLinkOpeningTag($button);
        $html[] = $this->renderLinkContent($button);
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    public function renderLinkContent(SubButton $button): string
    {
        $html = [];

        $displayLabel = $button->getDisplay() != ButtonDisplayInterface::DISPLAY_ICON && $this->getLabel($button);
        $displayIcon = $button->getDisplay() != ButtonDisplayInterface::DISPLAY_LABEL && $button->getInlineGlyph();

        if ($displayIcon)
        {
            $html[] = $button->getInlineGlyph()->render();
        }

        if ($displayLabel)
        {
            $html[] = '<span>' . $this->getLabel($button) . '</span> ';
        }

        return implode('', $html);
    }

    public function renderLinkOpeningTag(SubButton $button): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = 'class="' . implode(' ', $this->determineClasses($button)) . '"';
        $html[] = 'title="' . htmlentities($this->getTitle($button)) . '"';
        $html[] = $this->renderAction($button);
        $html[] = '>';

        return implode(' ', $html);
    }
}