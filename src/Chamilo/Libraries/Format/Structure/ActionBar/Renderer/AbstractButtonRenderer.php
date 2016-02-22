<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\InlineGlyph;

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
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton
     */
    private $button;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton $button
     */
    public function __construct(AbstractButton $button)
    {
        $this->button = $button;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton $button
     */
    public function setButton(AbstractButton $button)
    {
        $this->button = $button;
    }

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
        return ($this->getButton()->getLabel() ? htmlspecialchars($this->getButton()->getLabel()) : null);
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
        return 'title="' . $this->getLabel() . '"';
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

        $displayLabel = $button->getDisplay() != AbstractButton :: DISPLAY_ICON && ! empty($label);
        $displayIcon = $button->getDisplay() != AbstractButton :: DISPLAY_LABEL && ! empty($imagePath);

        if ($displayIcon)
        {
            $html[] = $this->renderLinkContentImage();
        }

        if ($displayLabel)
        {
            $html[] = '<span>' . $label . '</span>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderLinkContentImage()
    {
        $button = $this->getButton();
        $label = $this->getLabel();
        $imagePath = $button->getImagePath();

        if ($imagePath instanceof InlineGlyph)
        {
            return $imagePath->render();
        }
        else
        {
            return '<img src="' . htmlentities($button->getImagePath()) . '" alt="' . $label . '" title="' . $label .
                 '"/>';
        }
    }
}