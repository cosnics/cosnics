<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Service;

use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;

/**
 *
 * @package Chamilo\Libraries\UserInterface\ActionBar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DropDownButtonRenderer
{
    public function render(DropDownButton $dropDownButton): string
    {
        $html = [];

        $html[] = '<div class="btn-group">';
        $html[] = $this->renderLink($dropDownButton);
        $html[] = $this->renderSubButtons($dropDownButton);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function determineClasses(DropDownButton $dropDownButton): array
    {
        $classes = array_merge(['btn', 'btn-default'], $dropDownButton->getClasses());

        $classes[] = 'dropdown-toggle';

        return $classes;
    }

    /**
     * @return string[]
     */
    public function determineDropdownClasses(DropDownButton $dropDownButton): array
    {
        return array_merge(['dropdown-menu'], $dropDownButton->getDropDownClasses());
    }

    public function getTitle(DropDownButton $dropDownButton): ?string
    {
        return htmlspecialchars(strip_tags($dropDownButton->getLabel()));
    }

    public function renderLink(DropDownButton $dropDownButton): string
    {
        $html = [];

        $html[] = $this->renderLinkOpeningTag($dropDownButton);
        $html[] = $this->renderLinkContent($dropDownButton);
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    public function renderLinkContent(DropDownButton $dropDownButton): string
    {
        $html = [];

        $displayLabel =
            $dropDownButton->getDisplay() != ButtonDisplayInterface::DISPLAY_ICON && $dropDownButton->getLabel();
        $displayIcon =
            $dropDownButton->getDisplay() != ButtonDisplayInterface::DISPLAY_LABEL && $dropDownButton->getInlineGlyph();

        if ($displayIcon)
        {
            $html[] = $dropDownButton->getInlineGlyph()->render();
        }

        if ($displayLabel)
        {
            $html[] = '<span>' . $dropDownButton->getLabel() . '</span> ';
        }

        $html[] = '<span class="caret"></span>';

        return implode('', $html);
    }

    public function renderLinkOpeningTag(DropDownButton $dropDownButton): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = 'data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button"';
        $html[] = 'class="' . implode(' ', $this->determineClasses($dropDownButton)) . '"';
        $html[] = 'title="' . htmlentities($this->getTitle($dropDownButton)) . '"';
        $html[] = '>';

        return implode(' ', $html);
    }

    public function renderSubButtons(DropDownButton $dropDownButton): string
    {
        $html = [];

        $html[] = '<ul class="' . implode(' ', $this->determineDropdownClasses($dropDownButton)) . '">';

        foreach ($dropDownButton->getDropDownButtons() as $subButton)
        {
            $rendererClassName = __NAMESPACE__ . '\\' . ClassnameUtilities::getInstance()->getClassnameFromObject(
                    $subButton
                ) . 'Renderer';
            $renderer = new $rendererClassName($subButton);
            $html[] = $renderer->render($subButton);
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}