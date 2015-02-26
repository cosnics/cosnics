<?php
namespace Chamilo\Core\Menu\Renderer\Menu\Type;

use Chamilo\Core\Menu\Renderer\Menu\Renderer;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Menu\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Bar extends Renderer
{
    const TYPE = 'bar';

    public function display_menu_header($current_section)
    {
        $html = array();
        $html[] = '<div class="navbar">';
        
        return implode("\n", $html);
    }

    public function display_menu_footer()
    {
        return '</div>';
    }
}
