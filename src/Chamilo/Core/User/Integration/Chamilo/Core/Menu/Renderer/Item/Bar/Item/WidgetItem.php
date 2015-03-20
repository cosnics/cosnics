<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WidgetItem extends Bar
{

    public function get_url()
    {
        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => Manager :: context(),
                Application :: PARAM_ACTION => Manager :: ACTION_VIEW_ACCOUNT));
        return $redirect->getUrl();
    }

    public function render()
    {
        $html = array();

        $title = $this->get_item()->get_titles()->get_translation(Translation :: get_instance()->get_language());
        $selected = $this->get_item()->is_selected();

        $html[] = '<ul>';

        $html[] = '<li' . ($selected ? ' class="current"' : '') . '>';
        $html[] = '<a' . ($selected ? ' class="current"' : '') . ' href="' . $this->get_url() . '">';

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

        $user = $this->get_menu_renderer()->get_user();
        $profilePhotoUrl = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $user->get_id()));

        $profileHtml = array();

        $profileHtml[] = '<div class="item-account">';
        $profileHtml[] = '<div class="item-account-photo">';

        $editProfilePicture = Translation :: get('EditProfilePictureOverlay', null, 'Chamilo\Core\User');

        $profileHtml[] = '<div class="item-account-photo-base">';
        $profileHtml[] = '<img src="' . htmlspecialchars($profilePhotoUrl->getUrl()) . '" />';
        $profileHtml[] = '<div class="item-account-photo-edit">';
        $profileHtml[] = '<a href="' . $this->get_url() . '">';
        $profileHtml[] = $editProfilePicture;
        $profileHtml[] = '</a>';
        $profileHtml[] = '</div>';
        $profileHtml[] = '</div>';

        $profileHtml[] = '</div>';
        $profileHtml[] = '<div class="item-account-data">';
        $profileHtml[] = '<span class="item-account-data-name">' . $user->get_fullname() . '</span>';
        $profileHtml[] = '<span class="item-account-data-email">' . $user->get_email() . '</span>';
        $profileHtml[] = '<span class="item-account-data-my-account"><a href="' . $this->get_url() . '">';

        $profileHtml[] = Translation :: get('MyAccount', null, 'Chamilo\Core\User') . '</a></span>';
        $profileHtml[] = '</div>';
        $profileHtml[] = '<div class="clear"></div>';

        $imagePath = Theme :: getInstance()->getImagePath(
            'Chamilo\Core\User\Integration\Chamilo\Core\Menu',
            'LogoutItem');

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => Manager :: context(),
                Application :: PARAM_ACTION => Manager :: ACTION_LOGOUT));

        $profileHtml[] = '<div class="item-account-logout">';
        $profileHtml[] = '<a href="' . $redirect->getUrl() . '">';
        $profileHtml[] = '<img src="' . $imagePath . '" />';
        $profileHtml[] = Translation :: get('Logout', null, 'Chamilo\Core\User');
        $profileHtml[] = '</a>';
        $profileHtml[] = '</div>';

        $profileHtml[] = '</div>';

        $html[] = implode(PHP_EOL, $profileHtml);

        $html[] = '</li>';
        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }
}
