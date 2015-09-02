<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\CalendarRendererProvider;
use Chamilo\Application\Calendar\Repository\CalendarRendererProviderRepository;

/**
 *
 * @package Chamilo\Application\Calendar\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ICalComponent extends Manager
{

    public function run()
    {
        $dataProvider = new CalendarRendererProvider(
            new CalendarRendererProviderRepository(),
            $this->get_user(),
            $this->get_user(),
            array(),
            \Chamilo\Application\Calendar\Ajax\Manager :: context());

        $events = $dataProvider->getAllEvents();
        var_dump($events);
    }
}