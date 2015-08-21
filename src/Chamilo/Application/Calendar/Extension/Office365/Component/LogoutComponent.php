<?php
namespace Chamilo\Application\Calendar\Extension\Office365\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Calendar\Extension\Office365\Manager;
use Chamilo\Application\Calendar\Extension\Office365\Service\Office365CalendarService;
use Chamilo\Application\Calendar\Extension\Office365\Repository\Office365CalendarRepository;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

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
        $office365CalendarService = new Office365CalendarService(Office365CalendarRepository :: getInstance());
        $isSuccessful = $office365CalendarService->logout();

        $nextAction = new Redirect(
            array(Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context()));
        $nextAction->toUrl();
    }
}
