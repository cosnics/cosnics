<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SplitDropdownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;

/**
 *
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SplitDropdownButtonRenderer
{
    public function render(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $html[] = '<div class="btn-group">';
        $html[] = $this->renderLink($splitDropDownButton);
        $html[] = $this->renderDropdown($splitDropDownButton);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function determineClasses(SplitDropdownButton $splitDropDownButton): array
    {
        $classes = array_merge(['btn', 'btn-default'], $splitDropDownButton->getClasses());

        if (!$splitDropDownButton->getAction())
        {
            $classes[] = 'disabled';
        }

        return $classes;
    }

    /**
     * @return string[]
     */
    public function determineDropdownActionClasses(SplitDropdownButton $splitDropDownButton): array
    {
        $classes = [];

        $classes[] = 'btn';
        $classes[] = 'btn-default';
        $classes[] = 'dropdown-toggle';

        return array_merge($classes, $splitDropDownButton->getClasses());
    }

    /**
     * @return string[]
     */
    public function determineDropdownClasses(SplitDropdownButton $splitDropDownButton): array
    {
        return array_merge(['dropdown-menu'], $splitDropDownButton->getDropDownClasses());
    }

    public function getTitle(SplitDropdownButton $splitDropDownButton): ?string
    {
        return htmlspecialchars(strip_tags($splitDropDownButton->getLabel()));
    }

    public function renderAction(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        if ($splitDropDownButton->getAction())
        {
            $html[] = 'href="' . htmlentities($splitDropDownButton->getAction()) . '"';

            if ($splitDropDownButton->getTarget())
            {
                $html[] = 'target="' . $splitDropDownButton->getTarget() . '"';
            }

            if ($splitDropDownButton->needsConfirmation())
            {
                $html[] = 'onclick="return confirm(\'' .
                    addslashes(htmlentities($splitDropDownButton->getConfirmationMessage())) . '\');"';
            }
        }

        return implode(' ', $html);
    }

    public function renderDropdown(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $linkHtml = [];

        $linkHtml[] = '<a';
        $linkHtml[] = 'class="' . implode(' ', $this->determineDropdownActionClasses($splitDropDownButton)) . '"';
        $linkHtml[] = 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button"';
        $linkHtml[] = '>';

        $html[] = implode(' ', $linkHtml);
        $html[] = '<span class="caret"></span>';
        $html[] = '<span class="sr-only"></span>';
        $html[] = '</a>';

        $html[] = $this->renderSubButtons($splitDropDownButton);

        return implode(PHP_EOL, $html);
    }

    public function renderLink(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $html[] = $this->renderLinkOpeningTag($splitDropDownButton);
        $html[] = $this->renderLinkContent($splitDropDownButton);
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    public function renderLinkContent(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $displayLabel = $splitDropDownButton->getDisplay() != ButtonDisplayInterface::DISPLAY_ICON &&
            $splitDropDownButton->getLabel();
        $displayIcon = $splitDropDownButton->getDisplay() != ButtonDisplayInterface::DISPLAY_LABEL &&
            $splitDropDownButton->getInlineGlyph();

        if ($displayIcon)
        {
            $html[] = $splitDropDownButton->getInlineGlyph()->render();
        }

        if ($displayLabel)
        {
            $html[] = '<span>' . $splitDropDownButton->getLabel() . '</span> ';
        }

        return implode('', $html);
    }

    public function renderLinkOpeningTag(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = 'class="' . implode(' ', $this->determineClasses($splitDropDownButton)) . '"';
        $html[] = 'title="' . htmlentities($this->getTitle($splitDropDownButton)) . '"';
        $html[] = $this->renderAction($splitDropDownButton);
        $html[] = '>';

        return implode(' ', $html);
    }

    public function renderSubButtons(SplitDropdownButton $splitDropDownButton): string
    {
        $html = [];

        $html[] = '<ul class="' . implode(' ', $this->determineDropdownClasses($splitDropDownButton)) . '">';

        foreach ($splitDropDownButton->getDropDownButtons() as $subButton)
        {
            $rendererClassName =
                __NAMESPACE__ . '\\' . ClassnameUtilities::getInstance()->getClassnameFromObject($subButton) .
                'Renderer';
            $renderer = new $rendererClassName($subButton);
            $html[] = $renderer->render($subButton);
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}