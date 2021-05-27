<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Application\Calendar\ActionsInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Actions implements ActionsInterface
{

    /**
     *
     * @see \Chamilo\Application\Calendar\ActionsInterface::getPrimary()
     */
    public function getPrimary(Application $application)
    {
        return [];
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\ActionsInterface::getAdditional()
     */
    public function getAdditional(Application $application)
    {
        $dropdownButton = new DropdownButton(
            Translation::get('TypeName', null, __NAMESPACE__), new FontAwesomeGlyph('google', [], null, 'fab')
        );
        $dropdownButton->setDropdownClasses('dropdown-menu-right');

        $configurationContext = Manager::context();
        $accessToken = LocalSetting::getInstance()->get('token', $configurationContext);

        if (!$accessToken)
        {
            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGIN;

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $dropdownButton->addSubButton(
                new SubButton(Translation::get('GoogleCalendarLogin'), new FontAwesomeGlyph('sign-in-alt'), $link)
            );
        }
        else
        {
            $parameters = [];
            $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGOUT;

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $dropdownButton->addSubButton(
                new SubButton(Translation::get('GoogleCalendarLogout'), new FontAwesomeGlyph('sign-out-alt'), $link)
            );
        }

        return array($dropdownButton);
    }
}