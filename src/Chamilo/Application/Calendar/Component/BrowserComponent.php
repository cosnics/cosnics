<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionActionProviderCollection;
use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderCollection;
use Chamilo\Application\Calendar\Implementation\Libraries\CalendarRendererProvider;
use Chamilo\Application\Calendar\Manager;
use Chamilo\Core\User\Component\SettingsComponent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Calendar\Service\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SplitDropdownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use DateTime;
use Detection\MobileDetect;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager
{
    protected CalendarRendererProvider $calendarRendererProvider;

    private int $currentTime;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run(): Response
    {
        $this->checkAuthorization(Manager::CONTEXT);
        $this->checkLoggedInAs();

        $this->getPageConfiguration()->addCssFile(
            $this->getWebPathBuilder()->getCssPath(Manager::CONTEXT) . 'print.' .
            $this->getThemeWebPathBuilder()->getTheme() . '.min.css', 'print'
        );

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = '<div class="row">';
        $html[] = $this->renderNormalCalendar();
        $html[] = '</div>';
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function checkLoggedInAs(): void
    {
        $asAdmin = $this->getSession()->get('_as_admin');

        if ($asAdmin && $asAdmin > 0) {
            $user = $this->getUserService()->findUserByIdentifier($asAdmin);
            if (!$user instanceof User || !$user->isPlatformAdministrator()) {
                throw new NotAllowedException();
            }
        }
    }

    protected function getCalendarExtensionActionProvider(): CalendarExtensionActionProviderCollection
    {
        return $this->getService(CalendarExtensionActionProviderCollection::class);
    }

    protected function getCalendarExtensionDataProvider(): CalendarExtensionDataProviderCollection
    {
        return $this->getService(CalendarExtensionDataProviderCollection::class);
    }

    protected function getCalendarRendererFactory(): HtmlCalendarRendererFactory
    {
        return $this->getService(HtmlCalendarRendererFactory::class);
    }

    protected function getCalendarRendererProvider(): CalendarRendererProvider
    {
        if (!isset($this->calendarRendererProvider)) {
            $displayParameters = [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_BROWSE,
                HtmlCalendarRenderer::PARAM_TYPE => $this->getCurrentRendererType(),
                HtmlCalendarRenderer::PARAM_TIME => $this->getCurrentRendererTime()
            ];

            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getVisibilityRepository(), $this->getUser(), $displayParameters, Manager::CONTEXT
            );
        }

        return $this->calendarRendererProvider;
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

    public function getCurrentRendererType(): string
    {
        $rendererType = $this->getRequest()->query->get(HtmlCalendarRenderer::PARAM_TYPE);

        if (!$rendererType) {
            $rendererType = $this->getUserSettingService()->getSettingForUser(
                $this->getUser(), 'Chamilo\Libraries', 'calendar_default_view'
            );

            if ($rendererType == HtmlCalendarRenderer::TYPE_MONTH) {
                $detect = new MobileDetect();

                try {
                    if ($detect->isMobile() && !$detect->isTablet()) {
                        $rendererType = HtmlCalendarRenderer::TYPE_LIST;
                    }
                }
                catch (Exception) {
                }
            }
        }

        return $rendererType;
    }

    protected function getGeneralActions(): ButtonGroup
    {
        $translator = $this->getTranslator();
        $buttonGroup = new ButtonGroup();

        $printUrl = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_PRINT,
                HtmlCalendarRenderer::PARAM_TYPE => $this->getCurrentRendererType(),
                HtmlCalendarRenderer::PARAM_TIME => $this->getCurrentRendererTime()
            ]
        );

        $buttonGroup->addButton(
            new Button(
                $translator->trans('PrinterComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('print'), $printUrl
            )
        );

        $iCalUrl = $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => Manager::ACTION_ICAL]
        );

        $buttonGroup->addButton(
            new Button(
                $translator->trans('ICalExternal', [], Manager::CONTEXT), new FontAwesomeGlyph('globe'), $iCalUrl
            )
        );

        $settingsUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_SETTINGS,
                SettingsComponent::PARAM_SELECTED_CONTEXT => 'Chamilo\Core\User'
            ]
        );

        $splitDropdownButton = new SplitDropdownButtonCollection(
            $translator->trans('ConfigComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('cog'), $settingsUrl,
            ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL, null, [], null, ['dropdown-menu-right']
        );

        $availabilityUrl = $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => Manager::ACTION_AVAILABILITY]
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
    protected function getViewActions(): array
    {
        $actions = [];

        $primaryExtensionActions = [];
        $additionalExtensionActions = [];

        foreach ($this->getCalendarExtensionActionProvider()->getCalendarExtenstionActionProviders() as $actionProvider)
        {
            $primaryExtensionActions = array_merge($primaryExtensionActions, $actionProvider->getPrimary($this));
            $additionalExtensionActions = array_merge(
                $additionalExtensionActions, $actionProvider->getAdditional($this)
            );
        }

        $actions = array_merge($actions, $primaryExtensionActions);
        $actions = array_merge($actions, $additionalExtensionActions);

        $actions[] = $this->getGeneralActions();

        return $actions;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    protected function renderNormalCalendar(): string
    {
        $renderer = $this->getCalendarRendererFactory()->getRenderer($this->getCurrentRendererType());

        return $renderer->render(
            $this->getCalendarRendererProvider(), $this->getCurrentRendererTime(), $this->getViewActions()
        );
    }

    public function setCurrentRendererTime(int $currentTime): static
    {
        $this->currentTime = $currentTime;

        return $this;
    }
}
