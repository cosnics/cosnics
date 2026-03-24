<?php
namespace Chamilo\Application\Calendar\Extension\Google\Implementation\Calendar;

use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionActionProviderInterface;
use Chamilo\Application\Calendar\Extension\Google\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Core\User\Service\UserSettingsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
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

    protected UserSettingsService $userSettingsService;

    public function __construct(
        UrlGenerator $urlGenerator, Translator $translator, CalendarService $calendarService,
        UserSettingsService $userSettingsService
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->calendarService = $calendarService;
        $this->userSettingsService = $userSettingsService;
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
            DisplayTypeEnum::ICON_AND_LABEL, [], ['dropdown-menu-right']
        );

        $accessToken =
            $this->getUserSettingsService()->findUserSetting($user, 'cosnics.libraries.protocol.google.token');

        if (!$accessToken) {
            $link = $this->getUrlGenerator()->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::LOGIN->value
                ]
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
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::LOGOUT->value
                ]
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

    public function getUserSettingsService(): UserSettingsService
    {
        return $this->userSettingsService;
    }
}