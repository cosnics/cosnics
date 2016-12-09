<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: toolbar_item.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.html.toolbar
 */
class ToolbarItem
{
    const DISPLAY_ICON = 1;
    const DISPLAY_LABEL = 2;
    const DISPLAY_ICON_AND_LABEL = 3;

    private $label;

    private $display;

    private $image;

    private $href;

    private $confirmation;

    private $class;

    private $target;

    private $confirm_message;

    private $extraAttributes;

    /**
     *
     * @param string $label
     * @param string $image
     * @param string $href
     * @param int $display
     * @param boolean|string $confirmation
     * @param string $class
     * @param string $target
     * @param string $confirm_message
     */
    public function __construct($label = null, $image = null, $href = null, $display = self :: DISPLAY_ICON_AND_LABEL, $confirmation = false, $class = null, $target = null, 
        $confirm_message = null, $extraAttributes = null)
    {
        $this->label = $label;
        $this->display = $display;
        $this->image = $image;
        $this->href = $href;
        $this->confirmation = $confirmation;
        $this->class = $class;
        $this->target = $target;
        if ($confirm_message == null)
        {
            $this->confirm_message = Translation::get('Confirm', null, Utilities::COMMON_LIBRARIES);
        }
        else
        {
            $this->confirm_message = $confirm_message;
        }
        $this->extraAttributes = $extraAttributes;
    }

    public function get_label()
    {
        return $this->label;
    }

    public function set_label($label)
    {
        $this->label = $label;
    }

    public function get_display()
    {
        return $this->display;
    }

    public function set_display($display)
    {
        $this->display = $display;
    }

    public function get_image()
    {
        return $this->image;
    }

    public function set_image($image)
    {
        $this->image = $image;
    }

    public function get_href()
    {
        return $this->href;
    }

    public function set_href($href)
    {
        $this->href = $href;
    }

    public function get_target()
    {
        return $this->target;
    }

    public function set_target($target)
    {
        $this->target = $target;
    }

    public function get_confirmation()
    {
        return $this->confirmation;
    }

    public function set_confirmation($confirmation)
    {
        $this->confirmation = $confirmation;
    }

    function get_confirm_message()
    {
        return $this->confirm_message;
    }

    function set_confirm_message($message)
    {
        $this->confirm_message = $message;
    }

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
     * @return string
     */
    public function getExtraAttributes()
    {
        return $this->extraAttributes;
    }

    /**
     *
     * @param string $extraAttributes
     */
    public function setExtraAttributes($extraAttributes)
    {
        $this->extraAttributes = $extraAttributes;
    }

    public function as_html()
    {
        $label = ($this->get_label() ? htmlspecialchars($this->get_label()) : null);
        if (! $this->get_display())
        {
            $this->display = self::DISPLAY_ICON;
        }
        $display_label = ($this->display & self::DISPLAY_LABEL) == self::DISPLAY_LABEL && ! empty($label);
        
        $button = '';
        if (($this->display & self::DISPLAY_ICON) == self::DISPLAY_ICON && isset($this->image))
        {
            $button .= '<img src="' . htmlentities($this->image) . '" alt="' . $label . '" title="' .
                 htmlentities($label) . '"' . ($display_label ? ' class="labeled"' : '') . '/>';
        }
        
        if ($this->class)
        {
            $class = ' class="' . $this->class . '"';
        }
        else
        {
            $class = '';
        }
        
        if ($display_label)
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
        
        if ($this->get_href())
        {
            if ($this->get_confirmation() === true)
            {
                $this->set_confirmation(Translation::get($this->confirm_message));
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
            
            $button = '<a' . $class . $target . ' href="' . htmlentities($this->href) . '" title="' .
                 htmlentities($label) . '"' .
                 ($this->needs_confirmation() ? ' onclick="return confirm(\'' .
                 addslashes(htmlentities($this->get_confirmation())) . '\');"' : '') . ' ' . $extraAttributesString . '>' .
                 $button . '</a>';
        }
        
        return $button;
    }

    /**
     *
     * @return string
     */
    public function getClasses()
    {
        return $this->class;
    }
}
