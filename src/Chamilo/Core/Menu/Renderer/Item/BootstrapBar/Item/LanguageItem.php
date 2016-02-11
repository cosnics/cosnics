<?php
namespace Chamilo\Core\Menu\Renderer\Item\BootstrapBar\Item;

use Chamilo\Core\Menu\Renderer\Item\BootstrapBar\BootstrapBar;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LanguageItem extends BootstrapBar
{

    public function isItemSelected()
    {
        return false;
    }

    public function getContent()
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_QUICK_LANG,
                \Chamilo\Core\User\Manager :: PARAM_CHOICE => $this->getItem()->get_language(),
                \Chamilo\Core\User\Manager :: PARAM_REFER => $this->getItem()->getCurrentUrl()));

        $html[] = '<a href="' . $redirect->getUrl() . '">';
        $html[] = '<div class="chamilo-menu-item-label">';
        $html[] = $this->getItem()->get_language();
        $html[] = '</div>';
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}
