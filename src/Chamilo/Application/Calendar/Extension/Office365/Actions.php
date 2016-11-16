<?php
namespace Chamilo\Application\Calendar\Extension\Office365;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Actions implements \Chamilo\Application\Calendar\ActionsInterface
{

    /**
     *
     * @see \Chamilo\Application\Calendar\ActionsInterface::getPrimary()
     */
    public function getPrimary(Application $application)
    {
        return array();
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\ActionsInterface::getAdditional()
     */
    public function getAdditional(Application $application)
    {
        $dropdownButton = new DropdownButton(
            Translation::get('TypeName', null, __NAMESPACE__), 
            new FontAwesomeGlyph('windows'));
        $dropdownButton->setDropdownClasses('dropdown-menu-right');
        
        $configurationContext = \Chamilo\Application\Calendar\Extension\Office365\Manager::context();
        $accessToken = LocalSetting::getInstance()->get('token', $configurationContext);
        
        if (! $accessToken)
        {
            $parameters = array();
            $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGIN;
            
            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();
            
            $dropdownButton->addSubButton(
                new SubButton(Translation::get('Office365CalendarLogin'), new BootstrapGlyph('log-in'), $link));
        }
        else
        {
            $parameters = array();
            $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGOUT;
            
            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();
            
            $dropdownButton->addSubButton(
                new SubButton(Translation::get('Office365CalendarLogout'), new BootstrapGlyph('log-out'), $link));
        }
        
        return array($dropdownButton);
    }
}