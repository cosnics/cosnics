<?php
namespace Chamilo\Libraries\Calendar\Architecture\Traits;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Service\View\Table\CalendarTable;

/**
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait TableRendererTrait
{

    private CalendarTable $calendar;

    /**
     * @throws \ReflectionException
     */
    public function getCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime): CalendarTable
    {
        if (!isset($this->calendar))
        {
            $this->calendar = $this->initializeCalendar($dataProvider, $displayTime);
        }

        return $this->calendar;
    }

    public function setCalendar(CalendarTable $calendar)
    {
        $this->calendar = $calendar;
    }

    abstract public function initializeCalendar(CalendarRendererProviderInterface $dataProvider, int $displayTime
    ): CalendarTable;
}
