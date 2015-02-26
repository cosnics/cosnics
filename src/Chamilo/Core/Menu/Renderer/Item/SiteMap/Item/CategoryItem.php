<?php
namespace Chamilo\Core\Menu\Renderer\Item\SiteMap\Item;

use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\SiteMap\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CategoryItem extends Renderer
{

    public function render()
    {
        $html = array();
        
        $html[] = '<div class="category">';
        $html[] = '<h1>';
        $html[] = '<a href="#">' .
             $this->get_item()->get_titles()->get_translation(Translation :: get_instance()->get_language()) . '</a>';
        $html[] = '</h1>';
        
        if ($this->get_item()->has_children())
        {
            foreach ($this->get_item()->get_children() as $child)
            {
                $html[] = Renderer :: as_html($this->get_menu_renderer(), $child);
            }
        }
        
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}
