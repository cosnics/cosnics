<?php
namespace Chamilo\Libraries\Calendar\Service\Event;

use Chamilo\Libraries\Calendar\Event\Event;

/**
 * @package Chamilo\Libraries\Calendar\Service\Event
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventDayRenderer extends TableEventRenderer
{

    public function getPostfixSymbol(): string
    {
        return $this->getSymbol('chevron-down');
    }

    public function getPrefixSymbol(): string
    {
        return $this->getSymbol('chevron-up');
    }

    public function showPostfixDate(Event $event, int $cellStartDate, int $cellEndDate): bool
    {
        $startDate = $event->getStartDate();
        $endDate = $event->getEndDate();

        return ($startDate != $endDate) && ($endDate < $cellEndDate && $startDate < $cellStartDate);
    }

    public function showPostfixSymbol(Event $event, int $cellEndDate): bool
    {
        $startDate = $event->getStartDate();
        $endDate = $event->getEndDate();

        return ($startDate != $endDate) && ($endDate > $cellEndDate);
    }

    public function showPrefixDate(Event $event, int $cellStartDate, int $cellEndDate): bool
    {
        $startDate = $event->getStartDate();
        $endDate = $event->getEndDate();

        return ($startDate >= $cellStartDate && $startDate <= $cellEndDate &&
            ($startDate != $cellStartDate || $endDate < $cellEndDate));
    }

    public function showPrefixSymbol(Event $event, int $cellStartDate): bool
    {
        return ($event->getStartDate() < $cellStartDate);
    }
}