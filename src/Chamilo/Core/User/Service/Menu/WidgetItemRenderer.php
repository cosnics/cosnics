<?php
namespace Chamilo\Core\User\Service\Menu;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\Renderer\ItemRenderer;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Rights\Structure\Service\Interfaces\AuthorizationCheckerInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WidgetItemRenderer extends ItemRenderer
{

    protected UrlGenerator $urlGenerator;

    private ConfigurationConsulter $configurationConsulter;

    private UserPictureProviderInterface $userPictureProvider;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker, Translator $translator,
        CachedItemService $itemCacheService, ChamiloRequest $request, ConfigurationConsulter $configurationConsulter,
        UserPictureProviderInterface $userPictureProvider, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($authorizationChecker, $translator, $itemCacheService, $request);

        $this->configurationConsulter = $configurationConsulter;
        $this->userPictureProvider = $userPictureProvider;
        $this->urlGenerator = $urlGenerator;
    }

    public function render(Item $item, User $user): string
    {
        $translator = $this->getTranslator();

        if (!$this->isItemVisibleForUser($user))
        {
            return '';
        }

        $userPicture = $this->getUserPictureProvider()->getUserPictureAsBase64String($user, $user);

        $html = [];

        $title = $this->getTranslator()->trans('MyAccount', [], 'Chamilo\Core\User');

        $html[] = '<li>';
        $html[] =
            '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">';

        if ($item->showIcon())
        {
            $html[] =
                '<img class="profile-picture img-circle img-thumbnail" src="' . $userPicture . '" title="' . $title .
                '" alt="' . $title . '" />';
        }

        if ($item->showTitle())
        {
            $html[] = '<div>' . $title . '</div>';
        }

        $html[] = '</a>';

        $html[] = '<ul class="dropdown-menu">';

        // Header
        $html[] = '<li><a><div >' . $user->get_fullname() . '</div></a></li>';

        // Divider
        $html[] = '<li role="separator" class="divider"></li>';

        // Change user profile picture
        if ($this->getConfigurationConsulter()->getSetting(
            [Manager::CONTEXT, 'allow_change_user_picture']
        ))
        {
            $html[] = '<li>';
            $html[] = '<a href="' . $this->getPictureUrl() . '">';
            $html[] = '<div>' . $translator->trans('EditProfilePicture', [], 'Chamilo\Core\User') . '</div>';
            $html[] = '</a>';
            $html[] = '</li>';
        }

        // Account
        $html[] = '<li>';
        $html[] = '<a href="' . $this->getAccountUrl() . '">';
        $html[] = '<div>' . $translator->trans('MyAccount', [], 'Chamilo\Core\User') . '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        // Settings
        $html[] = '<li>';
        $html[] = '<a href="' . $this->getSettingsUrl() . '">';
        $html[] = '<div>' . $translator->trans('Settings', [], 'Chamilo\Core\User') . '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        // Divider
        $html[] = '<li role="separator" class="divider"></li>';

        // Logout
        $html[] = '<li>';
        $html[] = '<a href="' . $this->getLogoutUrl() . '">';
        $html[] = '<div>' . $translator->trans('Logout', [], 'Chamilo\Core\User') . '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        $html[] = '</ul>';

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function getAccountUrl(): string
    {
        return $this->getUserUrl(Manager::ACTION_VIEW_ACCOUNT);
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getLogoutUrl(): string
    {
        return $this->getUserUrl(Manager::ACTION_LOGOUT);
    }

    public function getPictureUrl(): string
    {
        return $this->getUserUrl(Manager::ACTION_CHANGE_PICTURE);
    }

    public function getSettingsUrl(): string
    {
        return $this->getUserUrl(Manager::ACTION_USER_SETTINGS);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUserPictureProvider(): UserPictureProviderInterface
    {
        return $this->userPictureProvider;
    }

    public function getUserUrl(string $action): string
    {
        return $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => $action]
        );
    }

    public function isItemVisibleForUser(User $user): bool
    {
        return $this->getAuthorizationChecker()->isAuthorized($user, 'Chamilo\Core\User', 'ManageAccount');
    }

    public function renderTitle(Item $item): string
    {
        return $this->getTranslator()->trans('UserAccountWidget', [], Manager::CONTEXT);
    }
}