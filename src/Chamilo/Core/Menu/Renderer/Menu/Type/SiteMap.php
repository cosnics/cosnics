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
class SiteMap extends Renderer
{
    const TYPE = 'SiteMap';

    public function display_menu_header($numberOfItems = 0)
    {
        return null;
    }

    public function display_menu_footer()
    {
        return null;
    }
}
