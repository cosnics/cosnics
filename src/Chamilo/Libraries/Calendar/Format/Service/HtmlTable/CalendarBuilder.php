<?php
namespace Chamilo\Libraries\Calendar\Format\Service\HtmlTable;

use Symfony\Component\Translation\Translator;
use Chamilo\Libraries\Calendar\CalendarConfiguration;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service\HtmlTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarBuilder
{
    const TIME_PLACEHOLDER = '__TIME__';

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    private $calendarConfiguration;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Table\CalendarConfiguration $calendarConfiguration
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(CalendarConfiguration $calendarConfiguration, Translator $translator)
    {
        $this->calendarConfiguration = $calendarConfiguration;
        $this->translator = $translator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarConfiguration
     */
    protected function getCalendarConfiguration()
    {
        return $this->calendarConfiguration;
    }

    /**
     *
     * @return \Symfony\Component\Translation\Translator
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @return string
     */
    protected function getFirstDayOfTheWeek()
    {
        return $this->getCalendarConfiguration()->getFirstDayOfTheWeek();
    }

    /**
     *
     * @return integer
     */
    protected function getWorkingHoursStart()
    {
        return $this->getCalendarConfiguration()->getWorkingHoursStart();
    }

    /**
     *
     * @return integer
     */
    protected function getWorkingHoursEnd()
    {
        return $this->getCalendarConfiguration()->getWorkingHoursEnd();
    }

    /**
     *
     * @return boolean
     */
    protected function getHideNonWorkingHours()
    {
        return $this->getCalendarConfiguration()->getHideNonWorkingHours();
    }

    /**
     *
     * @param integer $displayTime
     * @param string[] $displayParameters
     * @return \Chamilo\Libraries\Calendar\HtmlTable\Calendar
     */
    abstract public function buildCalendar($displayTime, $displayParameters = []);
}

