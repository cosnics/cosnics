<?php
namespace Chamilo\Libraries\Calendar\Architecture\Interfaces;

/**
 * An interface which forces the implementing Application to provide a given set of methods
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarRendererDataProviderInterface
{

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getCalendarEvents(): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getCalendarEventsInPeriod(int $startTime, int $endTime): array;
}