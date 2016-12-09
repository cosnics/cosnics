<?php
namespace Chamilo\Core\Menu\Renderer\Item\SiteMap;

use Chamilo\Core\Menu\Renderer\Item\Renderer;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\SiteMap
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SiteMap extends Renderer
{

    public function render()
    {
        $html = array();
        
        if ($this->getItem()->get_parent() == 0)
        {
            $html[] = '<div class="category">';
            $html[] = '<h1>';
            $html[] = $this->get_item_url();
            $html[] = '</h1>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div class="item">' . $this->get_item_url() . '</div>';
        }
        return implode(PHP_EOL, $html);
    }
}
