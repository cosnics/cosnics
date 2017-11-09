<?php
namespace Chamilo\Core\Menu\Renderer\Item\SiteMap\Item;

use Chamilo\Core\Menu\Renderer\Item\Renderer;
use Chamilo\Libraries\Translation\Translation;

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
             $this->getItem()->get_titles()->get_translation(Translation::getInstance()->getLanguageIsocode()) . '</a>';
        $html[] = '</h1>';
        
        if ($this->getItem()->has_children())
        {
            foreach ($this->getItem()->get_children() as $child)
            {
                $html[] = Renderer::toHtml($this->getMenuRenderer(), $child);
            }
        }
        
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
