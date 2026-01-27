<?php
namespace Chamilo\Libraries\Calendar\Architecture\Interface;

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
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    public function getEvents(?int $startTime = null, ?int $endTime = null, bool $calculateRecurrence = false): array;

    /**
     * @return \Chamilo\Libraries\Calendar\Architecture\Domain\Event[]
     */
    public function getEventsInPeriod(int $startTime, int $endTime, bool $calculateRecurrence = true): array;

    public function getDataUser(): User;

    /**
     * @return string[]
     */
    public function getDisplayParameters(): array;
}