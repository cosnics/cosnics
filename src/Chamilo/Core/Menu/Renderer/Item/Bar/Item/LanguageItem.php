<?php
namespace Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LanguageItem extends Bar
{

    public function getContent()
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Manager :: context(),
                Application :: PARAM_ACTION => \Chamilo\Core\User\Manager :: ACTION_QUICK_LANG,
                \Chamilo\Core\User\Manager :: PARAM_CHOICE => $this->get_item()->get_language(),
                \Chamilo\Core\User\Manager :: PARAM_REFER => $this->get_item()->getCurrentUrl()));

        $html[] = '<a href="' . $redirect->getUrl() . '">';
        $html[] = $this->get_item()->get_language();
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}
