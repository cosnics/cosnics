<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Domain\UserDetailsRendererRegistry;
use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Chamilo\Core\User\Implementation\User\UserDetailsRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly BreadcrumbTrail $breadcrumbTrail,
        protected readonly ButtonToolBarRenderer $buttonToolBarRenderer, protected readonly TabsRenderer $tabsRenderer,
        protected readonly UserDetailsRendererRegistry $userDetailsRendererCollection
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);
        $userToRender = $this->userService->retrieveUserByIdentifier(Uuid::fromString($userIdentifier));

        $this->breadcrumbTrail->add(new Breadcrumb($userToRender->getFullName()));

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->buttonToolBarRenderer->render($this->getButtonToolBar($userToRender));
        $html[] = $this->tabsRenderer->renderNavigationAndContent(
            'userDetails', $this->getTabsCollection($userToRender, $currentUser), md5(UserDetailsRenderer::class)
        );
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getButtonToolBar(User $userToRender): ButtonToolBar
    {
        $translator = $this->getTranslator();

        $buttonToolBar = new ButtonToolBar();
        $commonActions = new ButtonGroup();
        $toolActions = new ButtonGroup();

        $editUrl = $this->userUrlGenerator->getUpdateUrl($userToRender);

        $commonActions->addButton(
            new Button(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $editUrl, DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $deleteUrl = $this->userUrlGenerator->getDeleteUrl($userToRender);

        $commonActions->addButton(
            new Button(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $changeUserUrl = $this->userUrlGenerator->getChangeUserUrl($userToRender);

        $toolActions->addButton(
            new Button(
                $translator->trans('LoginAsUser', [], Manager::CONTEXT), new FontAwesomeGlyph('mask'), $changeUserUrl,
                DisplayTypeEnum::ICON_AND_LABEL
            )
        );

        $buttonToolBar->addButton($commonActions);
        $buttonToolBar->addButton($toolActions);

        return $buttonToolBar;
    }

    protected function getTabsCollection(User $userToView, User $currentUser): TabsCollection
    {
        $tabsCollection = new TabsCollection();

        foreach (
            $this->userDetailsRendererCollection->getUserDetailsRenderers() as $userDetailsRendererClassName =>
            $userDetailsRenderer
        ) {
            if ($userDetailsRenderer->hasContentForUser($userToView, $currentUser)) {
                $tabsCollection->add(
                    $this->initializeContentTab(
                        $userDetailsRendererClassName, $userDetailsRenderer, $userToView, $currentUser
                    )
                );
            }
        }

        return $tabsCollection;
    }

    protected function initializeContentTab(
        string $userDetailsRendererClassName, UserDetailsRendererInterface $userDetailsRenderer, User $userToRender,
        User $currentUser
    ): ContentTab
    {
        return new ContentTab(
            md5($userDetailsRendererClassName), $userDetailsRenderer->renderTitle($userToRender, $currentUser),
            $userDetailsRenderer->renderUserDetails($userToRender, $currentUser), $userDetailsRenderer->getGlyph()
        );
    }
}
