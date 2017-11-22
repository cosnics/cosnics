<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LinkItem extends Bar
{

    public function isItemSelected()
    {
        return false;
    }

    public function getContent()
    {
        $html = array();
        $html[] = '<a href="' . $this->getItem()->get_url() . '" target="' . $this->getItem()->get_target_string() . '">';
        
        $html[] = '<div class="chamilo-menu-item-label">' .
             $this->getItem()->get_titles()->get_translation(Translation::getInstance()->getLanguageIsocode()) . '</div>';
        
        $html[] = '</a>';
        
        return implode(PHP_EOL, $html);
    }
}
