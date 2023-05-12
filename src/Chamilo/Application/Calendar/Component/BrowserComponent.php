<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\User\Component\UserSettingsComponent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Calendar\Architecture\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Form\JumpForm;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Calendar\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     * @var JumpForm
     */
    private $form;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT);
        $this->checkLoggedInAs();

        $this->getPageConfiguration()->addCssFile(
            $this->getPathBuilder()->getCssPath(self::package(), true) . 'print.' .
            $this->getThemePathBuilder()->getTheme() . '.min.css', 'print'
        );

        $this->set_parameter(HtmlCalendarRenderer::PARAM_TYPE, $this->getCurrentRendererType());
        $this->set_parameter(HtmlCalendarRenderer::PARAM_TIME, $this->getCurrentRendererTime());

        $html = [];

        $html[] = $this->render_header();
        $html[] = '<div class="row">';
        $html[] = $this->renderNormalCalendar();
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function checkLoggedInAs()
    {
        $sessionUtilities = $this->getSessionUtilities();
        $asAdmin = $sessionUtilities->get('_as_admin');

        if ($asAdmin && $asAdmin > 0)
        {

            $user = $this->getUserService()->findUserByIdentifier($asAdmin);
            if (!$user instanceof User || !$user->is_platform_admin())
            {
                throw new NotAllowedException();
            }
        }
    }

    protected function getCalendarDataProvider()
    {
        if (!isset($this->calendarDataProvider))
        {
            $displayParameters = [
                self::PARAM_CONTEXT => self::package(),
                self::PARAM_ACTION => self::ACTION_BROWSE,
                HtmlCalendarRenderer::PARAM_TYPE => $this->getCurrentRendererType(),
                HtmlCalendarRenderer::PARAM_TIME => $this->getCurrentRendererTime()
            ];

            $this->calendarDataProvider = new CalendarRendererProvider(
                new CalendarRendererProviderRepository(), $this->get_user(), $displayParameters,
                \Chamilo\Application\Calendar\Ajax\Manager::CONTEXT
            );
        }

        return $this->calendarDataProvider;
    }

    protected function getCalendarRendererFactory(): HtmlCalendarRendererFactory
    {
        return $this->getService(HtmlCalendarRendererFactory::class);
    }

    protected function getGeneralActions()
    {
        $buttonGroup = new ButtonGroup();

        $printUrl = new Redirect(
            [
                self::PARAM_CONTEXT => self::package(),
                self::PARAM_ACTION => self::ACTION_PRINT,
                HtmlCalendarRenderer::PARAM_TYPE => $this->getCurrentRendererType(),
                HtmlCalendarRenderer::PARAM_TIME => $this->getCurrentRendererTime()
            ]
        );

        $buttonGroup->addButton(
            new Button(
                Translation::get(self::ACTION_PRINT . 'Component'), new FontAwesomeGlyph('print'), $printUrl->getUrl()
            )
        );

        $iCalUrl = new Redirect(
            [Application::PARAM_CONTEXT => self::package(), self::PARAM_ACTION => Manager::ACTION_ICAL]
        );

        $buttonGroup->addButton(
            new Button(Translation::get('ICalExternal'), new FontAwesomeGlyph('globe'), $iCalUrl->getUrl())
        );

        $settingsUrl = new Redirect(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Core\User\Manager::ACTION_USER_SETTINGS,
                UserSettingsComponent::PARAM_CONTEXT => 'Chamilo\Libraries\Calendar'
            ]
        );

        $splitDropdownButton = new SplitDropdownButton(
            Translation::get('ConfigComponent'), new FontAwesomeGlyph('cog'), $settingsUrl->getUrl(),
            SplitDropdownButton::DISPLAY_ICON_AND_LABEL, null, [], null, ['dropdown-menu-right']
        );

        $availabilityUrl = new Redirect(
            [Application::PARAM_CONTEXT => self::package(), self::PARAM_ACTION => Manager::ACTION_AVAILABILITY]
        );

        $splitDropdownButton->addSubButton(
            new SubButton(
                Translation::get('AvailabilityComponent'), new FontAwesomeGlyph('check-circle'),
                $availabilityUrl->getUrl()
            )
        );

        $buttonGroup->addButton($splitDropdownButton);

        return $buttonGroup;
    }

    protected function getViewActions()
    {
        $actions = [];

        $extensionRegistrations = Configuration::registrations_by_type(
            Manager::package() . '\Extension'
        );

        $primaryExtensionActions = [];
        $additionalExtensionActions = [];

        foreach ($extensionRegistrations as $extensionRegistration)
        {
            if ($extensionRegistration[Registration::PROPERTY_STATUS] == 1)
            {
                $actionRendererClass = $extensionRegistration[Registration::PROPERTY_CONTEXT] . '\Actions';
                $actionRenderer = new $actionRendererClass();

                $primaryExtensionActions = array_merge($primaryExtensionActions, $actionRenderer->getPrimary($this));
                $additionalExtensionActions = array_merge(
                    $additionalExtensionActions, $actionRenderer->getAdditional($this)
                );
            }
        }

        $actions = array_merge($actions, $primaryExtensionActions);
        $actions = array_merge($actions, $additionalExtensionActions);

        $actions[] = $this->getGeneralActions();

        return $actions;
    }

    protected function renderNormalCalendar()
    {
        $renderer = $this->getCalendarRendererFactory()->getRenderer($this->getCurrentRendererType());

        return $renderer->render(
            $this->getCalendarDataProvider(), $this->getCurrentRendererTime(), $this->getViewActions()
        );
    }

}
