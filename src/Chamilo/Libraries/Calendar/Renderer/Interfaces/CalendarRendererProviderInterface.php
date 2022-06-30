<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * An interface which forces the implementing Application to provide a given set of methods
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface CalendarRendererProviderInterface
{

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEvents(): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getAllEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array;

    public function getDataUser(): User;

    /**
     * @return string[]
     */
    public function getDisplayParameters(): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalEvents(): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getExternalEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEvents(): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Event\Event[]
     */
    public function getInternalEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array;

    public function getViewingUser(): User;
}