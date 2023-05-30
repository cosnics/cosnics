<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Application\Calendar\ActionsInterface;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Actions implements ActionsInterface
{
    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected UserSettingService $userSettingService;

    public function __construct(
        UrlGenerator $urlGenerator, UserSettingService $userSettingService, Translator $translator
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->userSettingService = $userSettingService;
        $this->translator = $translator;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getAdditional(Application $application): array
    {
        $translator = $this->getTranslator();

        $dropdownButton = new DropdownButton(
            $translator->trans('TypeName', [], __NAMESPACE__), new FontAwesomeGlyph('google', [], null, 'fab'),
            AbstractButton::DISPLAY_ICON_AND_LABEL, [], ['dropdown-menu-right']
        );

        $accessToken =
            $this->getUserSettingService()->getSettingForUser($application->getUser(), Manager::CONTEXT, 'token');

        if (!$accessToken)
        {
            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGIN;

            $link = $this->getUrlGenerator()->fromParameters($parameters);

            $dropdownButton->addSubButton(
                new SubButton(
                    $translator->trans('GoogleCalendarLogin', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('sign-in-alt'), $link
                )
            );
        }
        else
        {
            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGOUT;

            $link = $this->getUrlGenerator()->fromParameters($parameters);

            $dropdownButton->addSubButton(
                new SubButton(
                    $translator->trans('GoogleCalendarLogout', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('sign-out-alt'), $link
                )
            );
        }

        return [$dropdownButton];
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
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