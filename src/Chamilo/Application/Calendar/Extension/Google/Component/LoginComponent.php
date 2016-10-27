<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Application\Calendar\Extension\Google\Repository\CalendarRepository;
use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;

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
        $calendarService = new CalendarService(CalendarRepository :: getInstance());
        $result = $calendarService->login($this->getRequest()->query->get(CalendarService :: PARAM_AUTHORIZATION_CODE));

        if ($result)
        {
            $nextAction = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context(),
                    \Chamilo\Application\Calendar\Manager :: PARAM_ACTION => \Chamilo\Application\Calendar\Manager :: ACTION_AVAILABILITY));

            $nextAction->toUrl();
        }
    }
}
