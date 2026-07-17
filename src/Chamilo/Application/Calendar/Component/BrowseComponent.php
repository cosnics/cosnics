<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionActionProviderRegistry;
use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderRegistry;
use Chamilo\Application\Calendar\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarDataProvider;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Core\User\Architecture\Exception\NoSuchUserException;
use Chamilo\Core\User\Component\ConfigureComponent;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Calendar\Architecture\Enum\HtmlCalendarRendererTypeEnum;
use Chamilo\Libraries\Calendar\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Service\CalendarTableConfigurationBuilder;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use DateTime;
use Detection\MobileDetect;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowseComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        VisibilityRepository $visibilityRepository, protected CalendarDataProvider $calendarDataProvider,
        protected CalendarExtensionActionProviderRegistry $calendarExtensionActionProviderRegistry,
        protected CalendarExtensionDataProviderRegistry $calendarExtensionDataProviderRegistry,
        protected CalendarDataProvider $calendarRendererProvider,
        protected CalendarTableConfigurationBuilder $calendarTableConfigurationBuilder, protected string $defaultView,
        protected HtmlCalendarRendererFactory $htmlCalendarRendererFactory, protected PageHeaders $pageHeaders,
        protected UserService $userService, protected UserSettingsService $userSettingsService,
        protected WebPathBuilder $webPathBuilder, protected string $theme, protected ?int $currentTime = null
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $visibilityRepository
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser);
        $this->checkLoggedInAs();

        $this->pageHeaders->addCss(
            $this->getWebPathBuilder()->getCssPath(Manager::CONTEXT) . 'print.' . $this->theme . '.min.css', 'print'
        );

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = '<div class="row">';
        $html[] = $this->renderCalendar($currentUser);
        $html[] = '</div>';
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    protected function checkLoggedInAs(): void
    {
        $asAdmin = $this->getRequest()->getSession()->get('_as_admin');

        if ($asAdmin) {
            try {
                $user = $this->userService->findUserByIdentifier(Uuid::fromString($asAdmin));

                if (!$user->isPlatformAdministrator()) {
                    throw new NotAllowedException();
                }
            }
            catch (NoSuchUserException) {
                throw new NotAllowedException();
            }
        }
    }

    public function getCurrentRendererTime(): int
    {
        if (!isset($this->currentTime)) {
            $defaultRenderDate = new DateTime();
            $defaultRenderDate->setTime(0, 0);

            $this->currentTime = $this->getRequest()->query->get(
                HtmlCalendarRenderer::PARAM_TIME, $defaultRenderDate->getTimestamp()
            );
        }

        return $this->currentTime;
    }

    public function getCurrentRendererType(User $user): string
    {
        $rendererType = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TYPE);

        if (!$rendererType) {
            $rendererType = $this->userSettingsService->findUserSetting(
                $user, 'cosnics.libraries.calendar.defaultView', $this->defaultView
            );

            if ($rendererType == HtmlCalendarRendererTypeEnum::MONTH->value) {
                $detect = new MobileDetect();

                try {
                    if ($detect->isMobile() && !$detect->isTablet()) {
                        $rendererType = HtmlCalendarRendererTypeEnum::LIST->value;
                    }
                }
                catch (Exception) {
                }
            }
        }

        return $rendererType;
    }

    protected function getGeneralActions(User $user): ButtonGroup
    {
        $translator = $this->getTranslator();
        $buttonGroup = new ButtonGroup();

        $printUrl = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::PRINT->value,
                HtmlCalendarRenderer::PARAM_TYPE => $this->getCurrentRendererType($user),
                HtmlCalendarRenderer::PARAM_TIME => $this->getCurrentRendererTime()
            ]
        );

        $buttonGroup->addButton(
            new Button(
                $translator->trans('PrinterComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('print'), $printUrl
            )
        );

        $iCalUrl = $this->getUrlGenerator()->fromParameters(
            [ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => ActionEnum::ICAL->value]
        );

        $buttonGroup->addButton(
            new Button(
                $translator->trans('ICalExternal', [], Manager::CONTEXT), new FontAwesomeGlyph('globe'), $iCalUrl
            )
        );

        $settingsUrl = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => \Chamilo\Core\User\Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => \Chamilo\Core\User\Architecture\Enum\ActionEnum::CONFIGURE->value,
                ConfigureComponent::PARAM_SELECTED_CONTEXT => StringUtilities::LIBRARIES
            ]
        );

        $splitDropdownButton = new SplitDropdownButtonCollection(
            $translator->trans('ConfigComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('cog'), $settingsUrl,
            DisplayTypeEnum::ICON_AND_LABEL, null, [], null, ['dropdown-menu-right']
        );

        $availabilityUrl = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::AVAILABILITY->value
            ]
        );

        $splitDropdownButton->addButton(
            new SubButton(
                $translator->trans('AvailabilityComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('check-circle'),
                $availabilityUrl
            )
        );

        $buttonGroup->addButton($splitDropdownButton);

        return $buttonGroup;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface[]
     */
    protected function getViewActions(User $user): array
    {
        $actions = [];

        $primaryExtensionActions = [];
        $additionalExtensionActions = [];

        foreach (
            $this->calendarExtensionActionProviderRegistry->getCalendarExtenstionActionProviders() as $actionProvider
        ) {
            $primaryExtensionActions = array_merge($primaryExtensionActions, $actionProvider->getPrimary($user));
            $additionalExtensionActions = array_merge(
                $additionalExtensionActions, $actionProvider->getAdditional($user)
            );
        }

        $actions = array_merge($actions, $primaryExtensionActions);
        $actions = array_merge($actions, $additionalExtensionActions);

        $actions[] = $this->getGeneralActions($user);

        return $actions;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Exception
     */
    protected function renderCalendar(User $user): string
    {
        $renderer = $this->htmlCalendarRendererFactory->getHtmlCalendarRenderer($this->getCurrentRendererType($user));

        $displayParameters = [
            self::PARAM_CONTEXT => Manager::CONTEXT,
            self::PARAM_ACTION => ActionEnum::BROWSE->value,
            HtmlCalendarRenderer::PARAM_TYPE => $this->getCurrentRendererType($user),
            HtmlCalendarRenderer::PARAM_TIME => $this->getCurrentRendererTime()
        ];

        $calendarTableConfiguration = $this->calendarTableConfigurationBuilder->buildConfiguration($user);

        $events = $this->calendarDataProvider->getEvents(
            $user, $renderer->getEventsStartTime($calendarTableConfiguration, $this->getCurrentRendererTime()),
            $renderer->getEventsEndTime($calendarTableConfiguration, $this->getCurrentRendererTime())
        );

        return $renderer->render(
            $events, $calendarTableConfiguration, $displayParameters, $this->getCurrentRendererTime(),
            $this->getViewActions($user),
            $this->calendarDataProvider->getVisibilities($user->getIdentifier()->toString()), Manager::CONTEXT
        );
    }
}
