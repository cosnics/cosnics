<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionActionProviderCollection;
use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderCollection;
use Chamilo\Application\Calendar\Implementation\Libraries\CalendarRendererProvider;
use Chamilo\Application\Calendar\Manager;
use Chamilo\Core\User\Component\UserSettingsComponent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Calendar\Architecture\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements BreadcrumbLessComponentInterface
{

    protected CalendarRendererProvider $calendarRendererProvider;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT);
        $this->checkLoggedInAs();

        $this->getPageConfiguration()->addCssFile(
            $this->getWebPathBuilder()->getCssPath(Manager::CONTEXT) . 'print.' .
            $this->getThemeWebPathBuilder()->getTheme() . '.min.css', 'print'
        );

        $this->set_parameter(HtmlCalendarRenderer::PARAM_TYPE, $this->getCurrentRendererType());
        $this->set_parameter(HtmlCalendarRenderer::PARAM_TIME, $this->getCurrentRendererTime());

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = '<div class="row">';
        $html[] = $this->renderNormalCalendar();
        $html[] = '</div>';
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageNoResultException
     */
    protected function checkLoggedInAs(): void
    {
        $asAdmin = $this->getSession()->get('_as_admin');

        if ($asAdmin && $asAdmin > 0)
        {
            $user = $this->getUserService()->findUserByIdentifier($asAdmin);
            if (!$user instanceof User || !$user->isPlatformAdmin())
            {
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
        if (!isset($this->calendarRendererProvider))
        {
            $displayParameters = [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => self::ACTION_BROWSE,
                HtmlCalendarRenderer::PARAM_TYPE => $this->getCurrentRendererType(),
                HtmlCalendarRenderer::PARAM_TIME => $this->getCurrentRendererTime()
            ];

            $this->calendarRendererProvider = new CalendarRendererProvider(
                $this->getVisibilityRepository(), $this->getUser(), $displayParameters,
                \Chamilo\Application\Calendar\Manager::CONTEXT
            );
        }

        return $this->calendarRendererProvider;
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
                Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_USER_SETTINGS,
                UserSettingsComponent::PARAM_CONTEXT => 'Chamilo\Core\User'
            ]
        );

        $splitDropdownButton = new SplitDropdownButton(
            $translator->trans('ConfigComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('cog'), $settingsUrl,
            AbstractButton::DISPLAY_ICON_AND_LABEL, null, [], null, ['dropdown-menu-right']
        );

        $availabilityUrl = $this->getUrlGenerator()->fromParameters(
            [Application::PARAM_CONTEXT => Manager::CONTEXT, self::PARAM_ACTION => Manager::ACTION_AVAILABILITY]
        );

        $splitDropdownButton->addSubButton(
            new SubButton(
                $translator->trans('AvailabilityComponent', [], Manager::CONTEXT), new FontAwesomeGlyph('check-circle'),
                $availabilityUrl
            )
        );

        $buttonGroup->addButton($splitDropdownButton);

        return $buttonGroup;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function getViewActions()
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

}
