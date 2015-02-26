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

    public function get_item_url()
    {
        $html = array();
        if ($this->get_item()->get_parent() == 0)
        {
            $selected = $this->get_item()->is_selected();
        }
        
        $html[] = '<a' . ($selected ? ' class="current"' : '') . ' href="' . $this->get_item()->get_url() . '" target="' .
             $this->get_item()->get_target_string() . '">' .
             $this->get_item()->get_titles()->get_translation(Translation :: get_instance()->get_language());
        $html[] = '</a>';
        
        return implode("\n", $html);
    }
}
