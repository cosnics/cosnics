<?php
namespace Chamilo\Core\Menu\Renderer\Item\SiteMap\Item;

use Chamilo\Core\Menu\Renderer\Item\SiteMap\SiteMap;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\SiteMap\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LinkItem extends SiteMap
{

    public function get_item_url()
    {
        $html = array();
        $html[] = '<a href="' . $this->getItem()->get_url() . '"target="' . $this->getItem()->get_target_string() .
             '">' . $this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode());
        
        $html[] = '</a>';
        
        return implode(PHP_EOL, $html);
    }
}
