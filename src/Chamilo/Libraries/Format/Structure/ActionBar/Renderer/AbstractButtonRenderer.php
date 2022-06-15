<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractButtonRenderer extends AbstractButtonToolbarItemRenderer
{

    public function render():string
    {
        return $this->renderLink();
    }

    /**
     * @return string[]
     */
    public function determineClasses(): array
    {
        return array_merge(['btn', 'btn-default'], $this->getButton()->getClasses());
    }

    public function getLabel(): ?string
    {
        return ($this->getButton()->getLabel() ? $this->getButton()->getLabel() : null);
    }

    public function getTitle(): ?string
    {
        return htmlspecialchars(strip_tags($this->getLabel()));
    }

    public function renderClasses(): string
    {
        return 'class="' . implode(' ', $this->determineClasses()) . '"';
    }

    public function renderLink(): string
    {
        $html = [];

        $html[] = $this->renderLinkOpeningTag();
        $html[] = $this->renderLinkContent();
        $html[] = $this->renderLinkClosingTag();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderLinkClosingTag(): string
    {
        return '</a>';
    }

    public function renderLinkContent(): string
    {
        $button = $this->getButton();
        $label = $this->getLabel();
        $imagePath = $button->getInlineGlyph();

        $html = [];

        $displayLabel = $button->getDisplay() != AbstractButton::DISPLAY_ICON && !empty($label);
        $displayIcon = $button->getDisplay() != AbstractButton::DISPLAY_LABEL && !empty($imagePath);

        if ($displayIcon)
        {
            $html[] = $this->renderLinkContentImage();
        }

        if ($displayLabel)
        {
            $html[] = '<span>' . $label . '</span> ';
        }

        return implode('', $html);
    }

    public function renderLinkContentImage(): string
    {
        $inlineGlyph = $this->getButton()->getInlineGlyph();

        if ($inlineGlyph instanceof InlineGlyph)
        {
            return $inlineGlyph->render();
        }

        return '';
    }

    public function renderLinkOpeningTag(): string
    {
        $html = [];

        $html[] = '<a';
        $html[] = $this->renderClasses();
        $html[] = $this->renderTitle();
        $html[] = '>';

        return implode(' ', $html);
    }

    /**
     *
     * @return string
     */
    public function renderTitle(): string
    {
        return 'title="' . htmlentities($this->getTitle()) . '"';
    }
}