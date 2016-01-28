<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar\Renderer;

use Chamilo\Libraries\Format\Structure\ActionBar\Button;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ButtonRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\Button
     */
    private $button;

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\Button $button
     */
    public function __construct(Button $button)
    {
        $this->button = $button;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Button
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\Button $button
     */
    public function setButton(Button $button)
    {
        $this->button = $button;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $button = $this->getButton();

        $label = ($button->getLabel() ? htmlspecialchars($button->getLabel()) : null);

        $displayLabel = $button->getDisplay() != Button :: DISPLAY_ICON && ! empty($label);
        $displayIcon = $button->getDisplay() != Button :: DISPLAY_LABEL && ! empty($button->getImagePath());

        $html = array();

        $linkHtml = array();

        $linkHtml[] = '<a';
        $linkHtml[] = 'class="btn btn-default';

        if (! $button->getAction())
        {
            $linkHtml[] = 'disabled';
        }

        $linkHtml[] = $button->getClasses() . '"';

        if ($button->getAction())
        {
            $linkHtml[] = 'href="' . htmlentities($button->getAction()) . '"';
        }

        $linkHtml[] = 'title="' . $label . '"';

        if ($button->getTarget())
        {
            $linkHtml[] = 'target="' . $button->getTarget() . '"';
        }

        if ($button->needsConfirmation())
        {
            $linkHtml[] = 'onclick="return confirm(\'' . addslashes(htmlentities($button->getConfirmation())) . '\');"';
        }

        $linkHtml[] = '>';

        $html[] = implode(' ', $linkHtml);

        if ($displayIcon)
        {
            $html[] = '<img src="' . htmlentities($button->getImagePath()) . '" alt="' . $label . '" title="' . $label .
                 '"/>';
        }

        if ($displayLabel)
        {
            if ($button->getAction())
            {
                $html[] = '<span>' . $label . '</span>';
            }
            else
            {
                $html[] = '<span class="' . $button->getClasses() . '">' . $label . '</span>';
            }
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}