<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Service\GoogleCalendarService;
use Chamilo\Application\Calendar\Extension\Google\Repository\GoogleCalendarRepository;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

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
        $googleCalendarService = new GoogleCalendarService(GoogleCalendarRepository :: getInstance());
        $result = $googleCalendarService->login(
            $this->getRequest()->query->get(GoogleCalendarService :: PARAM_AUTHORIZATION_CODE));

        if ($result)
        {
            $nextAction = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Extension\Google\Manager :: context(),
                    \Chamilo\Application\Calendar\Extension\Google\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Extension\Google\Manager :: ACTION_VISIBILITY));

            $nextAction->toUrl();
        }
    }
}
