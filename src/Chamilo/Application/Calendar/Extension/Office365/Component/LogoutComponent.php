<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Component;

use Chamilo\Application\Calendar\Extension\Office365\Manager;
use Chamilo\Application\Calendar\Extension\Office365\Repository\CalendarRepository;
use Chamilo\Application\Calendar\Extension\Office365\Service\CalendarService;
use Chamilo\Application\Calendar\Repository\AvailabilityRepository;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Office365\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LogoutComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $calendarService = new CalendarService(CalendarRepository :: getInstance());
        $isSuccessful = $calendarService->logout();

        if ($isSuccessful)
        {
            $availabilityService = new AvailabilityService(new AvailabilityRepository());
            $availabilityService->deleteAvailabilityByCalendarType(self :: package());
        }

        $nextAction = new Redirect(
            array(Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context()));
        $nextAction->toUrl();
    }
}
