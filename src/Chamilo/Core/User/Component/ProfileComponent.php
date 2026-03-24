<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ProfileComponent extends Manager
{
    protected FormFactoryInterface $formFactory;

    protected TabsRenderer $tabsRenderer;

    protected Environment $twigEnvironment;

    protected bool $userCanChangePicture;

    protected ?UserPictureProviderInterface $userPictureProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, TabsRenderer $tabsRenderer, FormFactoryInterface $formFactory,
        Environment $twigEnvironment, ?UserPictureProviderInterface $userPictureProvider, bool $userCanChangePicture
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator
        );

        $this->tabsRenderer = $tabsRenderer;
        $this->userCanChangePicture = $userCanChangePicture;
        $this->userPictureProvider = $userPictureProvider;
        $this->twigEnvironment = $twigEnvironment;
        $this->formFactory = $formFactory;
    }

    public function canUserChangePicture(): bool
    {
        return $this->userCanChangePicture &&
            $this->getUserPictureProvider() instanceof UserPictureUpdateProviderInterface;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\LinkTab[]
     */
    public function getAvailableTabs(): array
    {
        $action = $this->getCurrentAction();
        $translator = $this->getTranslator();
        $tabs = [];

        $tabs[] = new LinkTab(
            ActionEnum::ACCOUNT->value,
            htmlentities($translator->trans(ActionEnum::ACCOUNT->value . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::ACCOUNT->value]
        ), ActionEnum::ACCOUNT->value == $action
        );

        if ($this->canUserChangePicture()) {
            $tabs[] = new LinkTab(
                ActionEnum::UPDATE_USER_PICTURE->value,
                htmlentities($translator->trans(ActionEnum::UPDATE_USER_PICTURE->value . 'Title', [], Manager::CONTEXT)
                ), new FontAwesomeGlyph('image', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
                [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::UPDATE_USER_PICTURE->value]
            ), ActionEnum::UPDATE_USER_PICTURE->value == $action
            );
        }

        $tabs[] = new LinkTab(
            ActionEnum::CONFIGURE->value,
            htmlentities($translator->trans(ActionEnum::CONFIGURE->value . 'Title', [], Manager::CONTEXT)),
            new FontAwesomeGlyph('cog', ['fa-lg'], null, 'fas'), $this->getUrlGenerator()->fromParameters(
            [self::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::CONFIGURE->value]
        ), ActionEnum::CONFIGURE->value == $action
        );

        return $tabs;
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    public function getTabsRenderer(): TabsRenderer
    {
        return $this->tabsRenderer;
    }

    public function getTwigEnvironment(): Environment
    {
        return $this->twigEnvironment;
    }

    public function getUserPictureProvider(): ?UserPictureUpdateProviderInterface
    {
        return $this->userPictureProvider;
    }

    protected function renderHeader(?User $user = null): string
    {
        $html = [];

        $html[] = parent::renderHeader($user);

        $availableTabs = $this->getAvailableTabs();

        if (count($availableTabs) > 1) {
            $tabs = new TabsCollection();

            foreach ($availableTabs as $availableTab) {
                $tabs->add($availableTab);
            }

            $html[] = $this->getTabsRenderer()->renderNavigation('profile', $tabs, $this->getCurrentAction());
        }

        return implode(PHP_EOL, $html);
    }
}
