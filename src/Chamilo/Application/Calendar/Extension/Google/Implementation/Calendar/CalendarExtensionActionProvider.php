<?php
namespace Chamilo\Application\Calendar\Extension\Google\Implementation\Calendar;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionActionProviderInterface;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
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

    protected UserService $userService;

    public function __construct(
        UrlGenerator $urlGenerator, UserService $userService, Translator $translator, CalendarService $calendarService
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->userService = $userService;
        $this->translator = $translator;
        $this->calendarService = $calendarService;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface[]
     */
    public function getAdditional(User $user): array
    {
        if (!$this->getCalendarService()->isConfigured()) {
            return [];
        }

        $translator = $this->getTranslator();

        $dropdownButton = new DropDownButtonCollection(
            $translator->trans('TypeName', [], Manager::CONTEXT), new FontAwesomeGlyph('google', [], null, 'fab'),
            ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL, [], ['dropdown-menu-right']
        );

        $accessToken = $this->getUserService()->findUserSetting($user, 'cosnics.libraries.protocol.google.token');

        if (!$accessToken) {
            $link = $this->getUrlGenerator()->fromParameters(
                [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGIN]
            );

            $dropdownButton->addButton(
                new SubButton(
                    $translator->trans('GoogleCalendarLogin', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('sign-in-alt'), $link
                )
            );
        }
        else {
            $link = $this->getUrlGenerator()->fromParameters(
                [Application::PARAM_CONTEXT => Manager::CONTEXT, Application::PARAM_ACTION => Manager::ACTION_LOGOUT]
            );

            $dropdownButton->addButton(
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
     * @return \Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonInterface[]
     */
    public function getPrimary(User $user): array
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

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}