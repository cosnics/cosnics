<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Table\CalendarConfiguration;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarBuilder
{
    const TIME_PLACEHOLDER = '__TIME__';

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
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
     * @return \Chamilo\Libraries\Calendar\Table\CalendarConfiguration
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
     * @param integer $displayTime
     * @param string[] $displayParameters
     * @param string[] $classes
     * @return \Chamilo\Libraries\Calendar\Table\Calendar
     */
    abstract public function buildCalendar($displayTime, $displayParameters = [], $classes = []);
}

