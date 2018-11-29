<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\Item;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Menu\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Menu\Renderer\ItemRenderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WidgetItemRenderer extends ItemRenderer
{

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @param \Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface $authorizationChecker
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator, ItemService $itemService,
        Theme $themeUtilities, ChamiloRequest $request, ConfigurationConsulter $configurationConsulter
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemService, $themeUtilities, $request);

        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return string
     */
    public function renderTitle(Item $item)
    {
        return $this->getTranslator()->trans('MyAccount', [], 'Chamilo\Core\User');
    }

    /**
     * @param \Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass\WidgetItem $item
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function render(Item $item, User $user)
    {
        $translator = $this->getTranslator();
        $themeUtilities = $this->getThemeUtilities();

        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $html = array();

        $title = $this->renderTitle($item);

        $html[] = '<li class="dropdown chamilo-account-menu-item">';
        $html[] = '<a href="' . $this->getAccountUrl() .
            '" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        if ($item->showIcon())
        {
            $profilePhotoUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                    Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                    Manager::PARAM_USER_USER_ID => $user->getId()
                )
            );

            $html[] = '<img class="chamilo-menu-item-icon chamilo-menu-item-icon-account' .
                ($item->showTitle() ? ' chamilo-menu-item-image-with-label' : '') . '
                " src="' . $profilePhotoUrl->getUrl() . '" title="' . $title . '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div class="chamilo-menu-item-label' .
                ($item->showIcon() ? ' chamilo-menu-item-label-with-image' : '') . '">' . $title . '</div>';
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</a>';

        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                Manager::PARAM_USER_USER_ID => $user->getId()
            )
        );

        $profileHtml = array();

        $profileHtml[] = '<ul class="dropdown-menu">';
        $profileHtml[] = '<li>';
        // $profileHtml[] = '<a href="#">';

        $profileHtml[] = '<div class="chamilo-menu-item-account">';
        $profileHtml[] = '<div class="chamilo-menu-item-account-photo">';

        $editProfilePicture = $translator->trans('EditProfilePictureOverlay', [], 'Chamilo\Core\User');

        $profileHtml[] = '<div class="chamilo-menu-item-account-photo-base">';
        $profileHtml[] = '<img src="' . htmlspecialchars($profilePhotoUrl->getUrl()) . '" />';

        if ($this->getConfigurationConsulter()->getSetting(
            array(Manager::context(), 'allow_change_user_picture')
        ))
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

        $profileHtml[] = '<a href="' . $this->getAccountUrl() . '">';
        $profileHtml[] = $translator->trans('MyAccount', [], 'Chamilo\Core\User');
        $profileHtml[] = '</a>';

        $profileHtml[] = ' - ';

        $profileHtml[] = '<a href="' . $this->getSettingsUrl() . '">';
        $profileHtml[] = $translator->trans('Settings', [], 'Chamilo\Core\User');
        $profileHtml[] = '</a>';

        $profileHtml[] = '</span>';
        $profileHtml[] = '</div>';
        $profileHtml[] = '<div class="clear"></div>';

        $imagePath = $themeUtilities->getImagePath('Chamilo\Core\User\Integration\Chamilo\Core\Menu', 'LogoutItem');

        $profileHtml[] = '<div class="chamilo-menu-item-account-logout">';
        $profileHtml[] = '<a href="' . $this->getLogoutUrl() . '">';
        $profileHtml[] = '<img src="' . $imagePath . '" />';
        $profileHtml[] = $translator->trans('Logout', [], 'Chamilo\Core\User');
        $profileHtml[] = '</a>';
        $profileHtml[] = '</div>';

        $profileHtml[] = '</div>';

        $profileHtml[] = '<div class="clear"></div>';

        $profileHtml[] = '</li>';
        $profileHtml[] = '</ul>';

        $html[] = implode(PHP_EOL, $profileHtml);

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->getUserUrl(Manager::ACTION_VIEW_ACCOUNT);
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->getUserUrl(Manager::ACTION_LOGOUT);
    }

    /**
     *
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->getUserUrl(Manager::ACTION_CHANGE_PICTURE);
    }

    /**
     *
     * @return string
     */
    public function getSettingsUrl()
    {
        return $this->getUserUrl(Manager::ACTION_USER_SETTINGS);
    }

    /**
     *
     * @param string $action
     *
     * @return string
     */
    public function getUserUrl($action)
    {
        $redirect = new Redirect(
            array(Application::PARAM_CONTEXT => Manager::context(), Application::PARAM_ACTION => $action)
        );

        return $redirect->getUrl();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function isItemVisibleForUser(User $user)
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\User', 'ManageAccount');
    }
}