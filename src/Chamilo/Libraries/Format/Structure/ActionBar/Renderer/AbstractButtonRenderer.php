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

    /**
     *
     * @return string
     */
    public function render()
    {
        return $this->renderLink();
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return ($this->getButton()->getLabel() ? $this->getButton()->getLabel() : null);
    }

    /**
     *
     * @return string
     */
    public function renderLink()
    {
        $html = array();

        $html[] = $this->renderLinkOpeningTag();
        $html[] = $this->renderLinkContent();
        $html[] = $this->renderLinkClosingTag();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderLinkOpeningTag()
    {
        $html = array();

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
    public function renderLinkClosingTag()
    {
        return '</a>';
    }

    /**
     *
     * @return string
     */
    public function renderClasses()
    {
        return 'class="' . implode(' ', $this->determineClasses()) . '"';
    }

    /**
     *
     * @return string[]
     */
    public function determineClasses()
    {
        $classes = array();

        $classes[] = 'btn';
        $classes[] = 'btn-default';
        $classes[] = $this->getButton()->getClasses();

        return $classes;
    }

    /**
     *
     * @return string
     */
    public function renderTitle()
    {
        return 'title="' . htmlentities($this->getTitle()) . '"';
    }

    public function getTitle()
    {
        return htmlspecialchars(strip_tags($this->getLabel()));
    }

    /**
     *
     * @return string
     */
    public function renderLinkContent()
    {
        $button = $this->getButton();
        $label = $this->getLabel();
        $imagePath = $button->getImagePath();

        $html = array();

        $displayLabel = $button->getDisplay() != AbstractButton::DISPLAY_ICON && ! empty($label);
        $displayIcon = $button->getDisplay() != AbstractButton::DISPLAY_LABEL && ! empty($imagePath);

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

    /**
     *
     * @return string
     */
    public function renderLinkContentImage()
    {
        $button = $this->getButton();
        $title = $this->getTitle();
        $imagePath = $button->getImagePath();

        if ($imagePath instanceof InlineGlyph)
        {
            return $imagePath->render();
        }
        else
        {
            return '<img src="' . htmlentities($button->getImagePath()) . '" alt="' . $title . '" title="' . $title .
                 '"/>';
        }
    }
}