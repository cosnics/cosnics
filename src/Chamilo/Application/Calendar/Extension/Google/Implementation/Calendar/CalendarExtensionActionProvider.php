<?php
namespace Chamilo\Application\Calendar\Extension\Google\Implementation\Calendar;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionActionProviderInterface;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\AbstractButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropdownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarExtensionActionProvider implements CalendarExtensionActionProviderInterface
{
    protected CalendarService $calendarService;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected UserSettingService $userSettingService;

    public function __construct(
        UrlGenerator $urlGenerator, UserSettingService $userSettingService, Translator $translator,
        CalendarService $calendarService
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->userSettingService = $userSettingService;
        $this->translator = $translator;
        $this->calendarService = $calendarService;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\AbstractButtonToolBarItem[]
     */
    public function getAdditional(Application $application): array
    {
        if (!$this->getCalendarService()->isConfigured())
        {
            return [];
        }

        $translator = $this->getTranslator();

        $dropdownButton = new DropdownButton(
            $translator->trans('TypeName', [], Manager::CONTEXT), new FontAwesomeGlyph('google', [], null, 'fab'),
            AbstractButton::DISPLAY_ICON_AND_LABEL, [], ['dropdown-menu-right']
        );

        $accessToken =
            $this->getUserSettingService()->getSettingForUser($application->getUser(), Manager::CONTEXT, 'token');

        if (!$accessToken)
        {
            $link = $this->getUrlGenerator()->fromParameters(
                [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGIN]
            );

            $dropdownButton->addSubButton(
                new SubButton(
                    $translator->trans('GoogleCalendarLogin', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('sign-in-alt'), $link
                )
            );
        }
        else
        {
            $link = $this->getUrlGenerator()->fromParameters(
                [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGOUT]
            );

            $dropdownButton->addSubButton(
                new SubButton(
                    $translator->trans('GoogleCalendarLogout', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('sign-out-alt'), $link
                )
            );
        }

        return [$dropdownButton];
    }

    public function getCalendarService(): CalendarService
    {
        return $this->calendarService;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\AbstractButtonToolBarItem[]
     */
    public function getPrimary(Application $application): array
    {
        return [];
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }
}