<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LoginComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $configuration = Configuration :: get_instance();

        $googleClient = new \Google_Client();
        $googleClient->setDeveloperKey($configuration->get_setting(array($this->package(), 'developer_key')));

        $calendarClient = new \Google_Service_Calendar($googleClient);

        $googleClient->setClientId($configuration->get_setting(array($this->package(), 'client_id')));
        $googleClient->setClientSecret($configuration->get_setting(array($this->package(), 'client_secret')));
        $googleClient->setScopes('https://www.googleapis.com/auth/calendar.readonly');

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Google\Manager :: context(),
                \Chamilo\Application\Calendar\Extension\Google\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Google\Manager :: ACTION_LOGIN));

        $googleClient->setRedirectUri($redirect->getUrl());

        $code = Request :: get('code');

        if (isset($code))
        {
            $googleClient->authenticate($code);
            $token = $googleClient->getAccessToken();

            LocalSetting :: create_local_setting('token', $token, $this->package());

            $googleClient->setAccessToken($token);

            $redirect = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context(),
                    \Chamilo\Application\Calendar\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Manager :: ACTION_BROWSE));

            $redirect->toUrl();
        }
        else
        {
            $url = $googleClient->createAuthUrl('https://www.googleapis.com/auth/calendar');
            header('Location: ' . $url);
            exit();
        }
    }
}
