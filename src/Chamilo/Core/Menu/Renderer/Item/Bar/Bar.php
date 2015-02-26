<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar;

use Chamilo\Core\Menu\Renderer\Item\Renderer;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Bar extends Renderer
{

    public function render()
    {
        $html = array();
        
        if ($this->get_item()->get_parent() == 0)
        {
            $html[] = '<ul>';
            $selected = $this->get_item()->is_selected();
        }
        
        $item_url = $this->get_item_url();
        $html[] = '<li' . ($selected ? ' class="current"' : '') . '>';
        $html[] = $item_url;
        
        $html[] = '</li>';
        
        if ($this->get_item()->get_parent() == 0)
        {
            $html[] = '</ul>';
        }
        
        return implode("\n", $html);
    }
}
