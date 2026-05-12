<?php
namespace Chamilo\Application\Calendar\Architecture\Interface;

use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Application\Calendar\Architecture
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarExtensionDataProviderInterface
{
    /**
     * Get the individual calendars in the implementing context
     *
     * @return \Chamilo\Application\Calendar\Architecture\Domain\AvailableCalendar[]
     */
    public function getCalendars(User $user): array;

    /**
     * Gets the events published in the implementing context
     *
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    public function getEvents(User $user, int $fromDate, int $toDate): array;

    public function getName(): string;
}