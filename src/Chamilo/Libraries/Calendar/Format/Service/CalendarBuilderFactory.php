<?php
namespace Chamilo\Libraries\Calendar\Format\Service;

use Chamilo\Libraries\Calendar\CalendarConfiguration;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarBuilderFactory
{

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
     * @param string $calendarType
     * @return \Chamilo\Libraries\Calendar\Format\Service\HtmlTable\CalendarBuilder
     */
    public function getCalendarBuilder($calendarType)
    {
        $className = 'Chamilo\Libraries\Calendar\Format\Service\HtmlTable\\' . $calendarType . 'CalendarBuilder';

        return new $className($this->getCalendarConfiguration(), $this->getTranslator());
    }
}

