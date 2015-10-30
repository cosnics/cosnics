<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LinkApplicationItem extends Bar
{

    public function isItemSelected()
    {
        // TODO: Implement this correctly?
        return false;
    }

    public function getContent()
    {
        $html = array();

        if ($this->getItem()->get_parent() == 0)
        {
            $selected = $this->isSelected();
        }

        $html[] = '<a' . ($selected ? ' class="current"' : '') . ' href="' . $this->getItem()->get_url() . '" target="' .
             $this->getItem()->get_target_string() . '">';

        $html[] = '<div class="label">' .
             $this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode()) .
             '</div>';

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}
