<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 */
class ToolbarItem
{
    const DISPLAY_ICON = 1;

    const DISPLAY_ICON_AND_LABEL = 3;

    const DISPLAY_LABEL = 2;

    /**
     *
     * @var string
     */
    private $label;

    /**
     *
     * @var integer
     */
    private $display;

    /**
     *
     * @var string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    private $image;

    /**
     *
     * @var string
     */
    private $href;

    /**
     *
     * @var boolean|string
     */
    private $confirmation;

    /**
     *
     * @var string
     */
    private $class;

    /**
     *
     * @var string
     */
    private $target;

    /**
     *
     * @var string
     */
    private $confirmationMessage;

    /**
     *
     * @var string[]
     */
    private $extraAttributes;

    /**
     *
     * @param string $label
     * @param string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $image
     * @param string $href
     * @param integer $display
     * @param boolean|string $confirmation
     * @param string $class
     * @param string $target
     * @param string $confirmationMessage
     * @param string[] $extraAttributes
     */
    public function __construct(
        $label = null, $image = null, $href = null, $display = self :: DISPLAY_ICON_AND_LABEL, $confirmation = false,
        $class = null, $target = null, $confirmationMessage = null, $extraAttributes = null
    )
    {
        $this->label = $label;
        $this->display = $display;
        $this->image = $image;
        $this->href = $href;
        $this->confirmation = $confirmation;
        $this->class = $class;
        $this->target = $target;

        if ($confirmationMessage == null)
        {
            $this->confirmationMessage = Translation::get('Confirm', null, Utilities::COMMON_LIBRARIES);
        }
        else
        {
            $this->confirmationMessage = $confirmationMessage;
        }

        $this->extraAttributes = $extraAttributes;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $label = ($this->get_label() ? htmlspecialchars($this->get_label()) : null);
        $display = !$this->get_display() ? self::DISPLAY_ICON : $this->get_display();

        $elementClasses = !empty($this->class) ? explode(' ', $this->class) : array();
        array_unshift($elementClasses, 'btn-link');

        $button = new Button(
            $label, $this->get_image(), $this->get_href(), $display, $this->get_confirmation(),
            implode(' ', $elementClasses), $this->get_target()
        );

        $buttonRenderer = new ButtonRenderer($button);

        return $buttonRenderer->render();

        $label = ($this->get_label() ? htmlspecialchars($this->get_label()) : null);
        if (!$this->get_display())
        {
            $this->display = self::DISPLAY_ICON;
        }

        $displayLabel = ($this->display & self::DISPLAY_LABEL) == self::DISPLAY_LABEL && !empty($label);

        $button = '';

        if (($this->display & self::DISPLAY_ICON) == self::DISPLAY_ICON && isset($this->image))
        {
            if (!$this->image instanceof InlineGlyph)
            {
                $button .= '<img src="' . htmlentities($this->image) . '" alt="' . $label . '" title="' .
                    htmlentities($label) . '"' . ($displayLabel ? ' class="labeled"' : '') . '/>';
            }
            else
            {
                $button .= $this->image->render();
            }
        }

        if ($this->class)
        {
            $class = ' class="btn btn-default ' . $this->class . '"';
        }
        else
        {
            $class = ' class="btn btn-default"';
        }

        if ($displayLabel)
        {
            if ($this->get_href())
            {
                $button .= '<span>' . $label . '</span>';
            }
            else
            {
                $button .= '<span' . $class . '>' . $label . '</span>';
            }
        }

        $elementName = $this->get_href() ? 'a' : 'div';

        $elementAttributes = array();

        if ($this->get_href())
        {
            if ($this->get_confirmation() === true)
            {
                $this->set_confirmation(Translation::get($this->confirmationMessage));
            }

            if ($this->target)
            {
                $target = ' target="' . $this->target . '"';
            }
            else
            {
                $target = '';
            }

            $extraAttributesString = array();

            foreach ($this->getExtraAttributes() as $extraAttributeKey => $extraAttributeValue)
            {
                $extraAttributesString[] = $extraAttributeKey . '="' . $extraAttributeValue . '"';
            }

            $extraAttributesString = implode(' ', $extraAttributesString);

            $html[] =
                '<a' . $class . $target . ' href="' . htmlentities($this->href) . '" title="' . htmlentities($label) .
                '"' . ($this->needs_confirmation() ?
                    ' onclick="return confirm(\'' . addslashes(htmlentities($this->get_confirmation())) . '\');"' :
                    '') . ' ' . $extraAttributesString . '>';
        }

        if (($this->display & self::DISPLAY_ICON) == self::DISPLAY_ICON && isset($this->image))
        {
            if (!$this->image instanceof InlineGlyph)
            {
                $html[] = '<img src="' . htmlentities($this->image) . '" alt="' . $label . '" title="' .
                    htmlentities($label) . '"' . ($displayLabel ? ' class="labeled"' : '') . '/>';
            }
            else
            {
                $html[] = $this->image->render();
            }
        }

        $html[] = '</' . $elementName . '>';

        return implode('', $html);
    }

    /**
     *
     * @return string
     * @deprecated Use render() now
     */
    public function as_html()
    {
        return $this->render();
    }

    /**
     *
     * @return string
     */
    public function getClasses()
    {
        return $this->class;
    }

    /**
     *
     * @return string
     */
    public function getExtraAttributes()
    {
        return $this->extraAttributes;
    }

    /**
     *
     * @param string[] $extraAttributes
     */
    public function setExtraAttributes($extraAttributes)
    {
        $this->extraAttributes = $extraAttributes;
    }

    /**
     *
     * @return string
     */
    function get_confirm_message()
    {
        return $this->confirmationMessage;
    }

    /**
     *
     * @return boolean|string
     */
    public function get_confirmation()
    {
        return $this->confirmation;
    }

    /**
     *
     * @param boolean|string $confirmation
     */
    public function set_confirmation($confirmation)
    {
        $this->confirmation = $confirmation;
    }

    /**
     *
     * @return integer
     */
    public function get_display()
    {
        return $this->display;
    }

    /**
     *
     * @param integer $display
     */
    public function set_display($display)
    {
        $this->display = $display;
    }

    /**
     *
     * @return string
     */
    public function get_href()
    {
        return $this->href;
    }

    /**
     *
     * @param string $href
     */
    public function set_href($href)
    {
        $this->href = $href;
    }

    /**
     *
     * @return string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph
     */
    public function get_image()
    {
        return $this->image;
    }

    /**
     *
     * @param string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $image
     */
    public function set_image($image)
    {
        $this->image = $image;
    }

    /**
     *
     * @return string
     */
    public function get_label()
    {
        return $this->label;
    }

    /**
     *
     * @param unknown $label
     */
    public function set_label($label)
    {
        $this->label = $label;
    }

    /**
     *
     * @return string
     */
    public function get_target()
    {
        return $this->target;
    }

    /**
     *
     * @param string $target
     */
    public function set_target($target)
    {
        $this->target = $target;
    }

    /**
     *
     * @return boolean
     */
    public function needs_confirmation()
    {
        if ($this->get_confirmation() === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     *
     * @param string $message
     */
    function set_confirm_message($message)
    {
        $this->confirmationMessage = $message;
    }
}
