<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\MenuItem;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountItem extends MenuItem
{

    /**
     *
     * @return string
     */
    public function getContent()
    {
        $html = array();

        if ($this->get_item()->get_parent() == 0)
        {
            $selected = $this->get_item()->is_selected();
        }

        $html[] = '<a' . ($selected ? ' class="current"' : '') . ' href="' . $this->get_url() . '">';

        $title = $this->get_item()->get_titles()->get_translation(Translation :: get_instance()->get_language());

        if ($this->get_item()->show_icon())
        {
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                $this->get_item()->get_type());
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent($itemNamespace, 2);
            $itemType = ClassnameUtilities :: getInstance()->getClassnameFromNamespace($this->get_item()->get_type());
            $imagePath = Theme :: getInstance()->getImagePath($itemNamespace, $itemType . ($selected ? 'Selected' : ''));

            $profilePhotoUrl = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                    \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $this->get_menu_renderer()->get_user()->get_id()));

            $html[] = '<img class="item-icon item-icon-account" src="' . $profilePhotoUrl->getUrl() . '" title="' .
                 $title . '" alt="' . $title . '" />';
        }

        if ($this->get_item()->show_title())
        {
            $html[] = $title;
        }

        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    public function get_url()
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => Manager :: context(),
                Application :: PARAM_ACTION => Manager :: ACTION_VIEW_ACCOUNT));
        return $redirect->getUrl();
    }
}
