<?php
namespace Chamilo\Core\User\Implementation\Menu;

use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\InlineGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Service\Menu
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WidgetItemRenderer extends ItemRenderer
{
    protected bool $canChangeUserPicture;

    protected UrlGenerator $urlGenerator;

    private UserPictureProviderInterface $userPictureProvider;

    public function __construct(
        Translator $translator, CachedItemService $itemCacheService, ChamiloRequest $request,
        UserPictureProviderInterface $userPictureProvider, UrlGenerator $urlGenerator, bool $canChangeUserPicture = true
    )
    {
        parent::__construct($translator, $itemCacheService, $request);

        $this->userPictureProvider = $userPictureProvider;
        $this->urlGenerator = $urlGenerator;
        $this->canChangeUserPicture = $canChangeUserPicture;
    }

    public function render(Item $item, User $user): string
    {
        $translator = $this->getTranslator();

        $userPicture = $this->getUserPictureProvider()->getUserPictureAsBase64String($user);

        $html = [];

        $title = $this->getTranslator()->trans('MyAccount', [], Manager::CONTEXT);

        $html[] = '<li class="nav-item dropdown">';
        $html[] = '<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">';

        if ($item->showIcon()) {
            $html[] =
                '<img class="img-profile img-thumbnail rounded-circle" src="' . $userPicture . '" title="' . $title .
                '" alt="' . $title . '" />';
        }

        if ($item->showTitle()) {
            $html[] = '<span>' . $title . '</span>';
        }

        $html[] = '</a>';

        $html[] = '<ul class="dropdown-menu dropdown-menu-end">';

        // Header
        $html[] = '<li><a class="dropdown-item"><div >' . $user->getFullName() . '</div></a></li>';

        // Divider
        $html[] = '<li><hr class="dropdown-divider"></li>';

        // Change user profile picture
        if ($this->canChangeUserPicture()) {
            $html[] = '<li>';
            $html[] = '<a class="dropdown-item" href="' . $this->getPictureUrl() . '">';
            $html[] = '<div>' . $translator->trans('EditProfilePicture', [], Manager::CONTEXT) . '</div>';
            $html[] = '</a>';
            $html[] = '</li>';
        }

        // Account
        $html[] = '<li>';
        $html[] = '<a class="dropdown-item" href="' . $this->getAccountUrl() . '">';
        $html[] = '<div>' . $translator->trans('MyAccount', [], Manager::CONTEXT) . '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        // Settings
        $html[] = '<li>';
        $html[] = '<a class="dropdown-item" href="' . $this->getSettingsUrl() . '">';
        $html[] = '<div>' . $translator->trans('Settings', [], Manager::CONTEXT) . '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        // Divider
        $html[] = '<li><hr class="dropdown-divider"></li>';

        // Logout
        $html[] = '<li>';
        $html[] = '<a class="dropdown-item" href="' . $this->getLogoutUrl() . '">';
        $html[] = '<div>' . $translator->trans('Logout', [], Manager::CONTEXT) . '</div>';
        $html[] = '</a>';
        $html[] = '</li>';

        $html[] = '</ul>';

        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    public function canChangeUserPicture(): bool
    {
        return $this->canChangeUserPicture;
    }

    public function getAccountUrl(): string
    {
        return $this->getUserUrl(ActionEnum::ACCOUNT->value);
    }

    public function getLogoutUrl(): string
    {
        return $this->getUserUrl(ActionEnum::LOGOUT->value);
    }

    public function getPictureUrl(): string
    {
        return $this->getUserUrl(ActionEnum::UPDATE_USER_PICTURE->value);
    }

    public function getRendererTypeGlyph(): InlineGlyph
    {
        return new FontAwesomeGlyph('address-card');
    }

    public function getRendererTypeName(): string
    {
        return $this->getTranslator()->trans('UserAccountWidget', [], Manager::CONTEXT);
    }

    public function getSettingsUrl(): string
    {
        return $this->getUserUrl(ActionEnum::CONFIGURE->value);
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
            [ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT, ApplicationInterface::PARAM_ACTION => $action]
        );
    }

    public function renderTitleForCurrentLanguage(Item $item): string
    {
        return $this->getRendererTypeName();
    }

    public function renderTitleForIsoCode(Item $item, string $isoCode): string
    {
        return $this->getTranslator()->trans('UserAccountWidget', [], \Chamilo\Core\Menu\Manager::CONTEXT, $isoCode);
    }
}