<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Menu\Renderer\Item\Bar\Bar;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item\Bar\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WidgetItem extends Bar
{

    public function isItemSelected()
    {
        return false;
    }

    /**
     *
     * @param string $action
     * @return string
     */
    public function getUserUrl($action)
    {
        $redirect = new Redirect(
            array(Application :: PARAM_CONTEXT => Manager :: context(), Application :: PARAM_ACTION => $action));
        return $redirect->getUrl();
    }

    /**
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->getUserUrl(Manager :: ACTION_VIEW_ACCOUNT);
    }

    /**
     *
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->getUserUrl(Manager :: ACTION_CHANGE_PICTURE);
    }

    /**
     *
     * @return string
     */
    public function getSettingsUrl()
    {
        return $this->getUserUrl(Manager :: ACTION_USER_SETTINGS);
    }

    /**
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->getUserUrl(Manager :: ACTION_LOGOUT);
    }

    /**
     *
     * @see \Chamilo\Core\Menu\Renderer\Item\Bar\Bar::render()
     */
    public function render()
    {
        if(!$this->canViewMenuItem($this->getMenuRenderer()->get_user()))
        {
            return '';
        }
        
        $html = array();

        $title = htmlentities($this->getItem()->get_titles()->get_translation(Translation :: getInstance()->getLanguageIsocode()));
        $selected = $this->isSelected();

        $html[] = '<li class="dropdown chamilo-account-menu-item">';
        $html[] = '<a href="' . $this->getAccountUrl() .
             '" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        if ($this->getItem()->show_icon())
        {
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                $this->getItem()->get_type());
            $itemNamespace = ClassnameUtilities :: getInstance()->getNamespaceParent($itemNamespace, 2);
            $itemType = ClassnameUtilities :: getInstance()->getClassnameFromNamespace($this->getItem()->get_type());
            $imagePath = Theme :: getInstance()->getImagePath($itemNamespace, $itemType . ($selected ? 'Selected' : ''));

            $profilePhotoUrl = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                    \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $this->getMenuRenderer()->get_user()->get_id()));

            $html[] = '<img class="chamilo-menu-item-icon chamilo-menu-item-icon-account' .
                ($this->getItem()->show_title() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' .
                 $profilePhotoUrl->getUrl() . '" title="' . $title . '" alt="' . $title . '" />';

            // $html[] = '<img class="chamilo-menu-item-icon chamilo-menu-item-icon-account"
        // src="https://chamilo.hogent.be/application/bamaflex/php/webservices/foto_call.class.php?user_id=7638"
        // title="' .
            // $title . '" alt="' . $title . '" />';
        }

        if ($this->getItem()->show_title())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                 ($this->getItem()->show_icon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';

        $user = $this->getMenuRenderer()->get_user();
        $profilePhotoUrl = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(),
                Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $user->get_id()));

        $profileHtml = array();

        $profileHtml[] = '<ul class="dropdown-menu">';
        $profileHtml[] = '<li>';
        // $profileHtml[] = '<a href="#">';

        $profileHtml[] = '<div class="chamilo-menu-item-account">';
        $profileHtml[] = '<div class="chamilo-menu-item-account-photo">';

        $editProfilePicture = Translation :: get('EditProfilePictureOverlay', null, 'Chamilo\Core\User');

        $profileHtml[] = '<div class="chamilo-menu-item-account-photo-base">';
        $profileHtml[] = '<img src="' . htmlspecialchars($profilePhotoUrl->getUrl()) . '" />';

        if (Configuration :: get(\Chamilo\Core\User\Manager :: context(), 'allow_change_user_picture'))
        {
            $profileHtml[] = '<div class="chamilo-menu-item-account-photo-edit">';
            $profileHtml[] = '<a href="' . $this->getPictureUrl() . '">';
            $profileHtml[] = $editProfilePicture;
            $profileHtml[] = '</a>';
            $profileHtml[] = '</div>';
        }

        $profileHtml[] = '</div>';

        $profileHtml[] = '</div>';
        $profileHtml[] = '<div class="chamilo-menu-item-account-data">';
        $profileHtml[] = '<span class="chamilo-menu-item-account-data-name">' . $user->get_fullname() . '</span>';
        $profileHtml[] = '<span class="chamilo-menu-item-account-data-email">' . $user->get_email() . '</span>';
        $profileHtml[] = '<span class="chamilo-menu-item-account-data-my-account">';

        $imagePath = Theme :: getInstance()->getImagePath(
            'Chamilo\Core\User\Integration\Chamilo\Core\Menu',
            'Widget/MyAccount');

        $profileHtml[] = '<a href="' . $this->getAccountUrl() . '">';
        $profileHtml[] = Translation :: get('MyAccount', null, 'Chamilo\Core\User');
        $profileHtml[] = '</a>';

        $profileHtml[] = ' - ';

        $imagePath = Theme :: getInstance()->getImagePath(
            'Chamilo\Core\User\Integration\Chamilo\Core\Menu',
            'Widget/Settings');

        $profileHtml[] = '<a href="' . $this->getSettingsUrl() . '">';
        $profileHtml[] = Translation :: get('Settings', null, 'Chamilo\Core\User');
        $profileHtml[] = '</a>';

        $profileHtml[] = '</span>';
        $profileHtml[] = '</div>';
        $profileHtml[] = '<div class="clear"></div>';

        $imagePath = Theme :: getInstance()->getImagePath(
            'Chamilo\Core\User\Integration\Chamilo\Core\Menu',
            'LogoutItem');

        $profileHtml[] = '<div class="chamilo-menu-item-account-logout">';
        $profileHtml[] = '<a href="' . $this->getLogoutUrl() . '">';
        $profileHtml[] = '<img src="' . $imagePath . '" />';
        $profileHtml[] = Translation :: get('Logout', null, 'Chamilo\Core\User');
        $profileHtml[] = '</a>';
        $profileHtml[] = '</div>';

        $profileHtml[] = '</div>';

        $profileHtml[] = '<div class="clear"></div>';

        // $profileHtml[] = '</a>';
        $profileHtml[] = '</li>';
        $profileHtml[] = '</ul>';

        $html[] = implode(PHP_EOL, $profileHtml);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns whether or not the given user can view this menu item
     *
     * @param User $user
     *
     * @return bool
     */
    public function canViewMenuItem(User $user)
    {
        $authorizationChecker = $this->getAuthorizationChecker();
        return $authorizationChecker->isAuthorized(
            $this->getMenuRenderer()->get_user(), 'Chamilo\Core\User', 'ManageAccount'
        );
    }
}
