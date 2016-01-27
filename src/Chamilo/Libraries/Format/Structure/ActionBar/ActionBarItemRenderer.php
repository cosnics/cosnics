<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Format\Structure\ToolbarItem;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionBarItemRenderer
{

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $toolbarItem
     */
    public function render(ToolbarItem $toolbarItem)
    {
        $label = ($toolbarItem->get_label() ? htmlspecialchars($toolbarItem->get_label()) : null);

        $displayLabel = $toolbarItem->get_display() != ToolbarItem :: DISPLAY_ICON && ! empty($label);
        $displayIcon = $toolbarItem->get_display() != ToolbarItem :: DISPLAY_LABEL && ! empty($toolbarItem->get_image());

        $html = array();

        $linkHtml = array();

        $linkHtml[] = '<a';
        $linkHtml[] = 'class="btn btn-default';

        if (! $toolbarItem->get_href())
        {
            $linkHtml[] = 'disabled';
        }

        $linkHtml[] = $toolbarItem->getClasses() . '"';

        if ($toolbarItem->get_href())
        {
            $linkHtml[] = 'href="' . htmlentities($toolbarItem->get_href()) . '"';
        }

        $linkHtml[] = 'title="' . $label . '"';

        if ($toolbarItem->get_target())
        {
            $linkHtml[] = 'target="' . $toolbarItem->get_target() . '"';
        }

        if ($toolbarItem->needs_confirmation())
        {
            $linkHtml[] = 'onclick="return confirm(\'' . addslashes(htmlentities($toolbarItem->get_confirm_message())) .
                 '\');"';
        }

        $linkHtml[] = '>';

        $html[] = implode(' ', $linkHtml);

        if ($displayIcon)
        {
            $html[] = '<img src="' . htmlentities($toolbarItem->get_image()) . '" alt="' . $label . '" title="' . $label .
                 '"/>';
        }

        if ($displayLabel)
        {
            if ($toolbarItem->get_href())
            {
                $html[] = '<span>' . $label . '</span>';
            }
            else
            {
                $html[] = '<span class="' . $toolbarItem->getClasses() . '">' . $label . '</span>';
            }
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}
