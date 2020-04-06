<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Form;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Libraries\Calendar\Event\Recurrence\RecurringContentObjectForm;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Form
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class CalendarEventForm extends RecurringContentObjectForm
{

    /**
     * @throws \Exception
     */
    public function addCalendarEventPropertiesToForm()
    {
        $this->addElement('category', Translation::get('Properties'));

        $this->add_datepicker(CalendarEvent::PROPERTY_START_DATE, Translation::get('StartDate'), true);
        $this->add_datepicker(CalendarEvent::PROPERTY_END_DATE, Translation::get('EndDate'), true);

        $this->addRule(
            CalendarEvent::PROPERTY_START_DATE,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
        );

        $this->addRule(
            CalendarEvent::PROPERTY_END_DATE,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
        );

        $this->addFrequencyPropertiesToForm();

        $this->add_textfield(CalendarEvent::PROPERTY_LOCATION, Translation::get('Location'), false);
    }

    /**
     * @param string[] $htmleditorOptions
     * @param boolean $inTab
     *
     * @throws \Exception
     */
    protected function build_creation_form($htmleditorOptions = array(), $inTab = false)
    {
        parent::build_creation_form($htmleditorOptions, $inTab);
        $this->addCalendarEventPropertiesToForm();
    }

    /**
     * @param string[] $htmleditorOptions
     * @param boolean $inTab
     *
     * @throws \Exception
     */
    protected function build_editing_form($htmleditorOptions = array(), $inTab = false)
    {
        parent::build_editing_form($htmleditorOptions, $inTab);
        $this->addCalendarEventPropertiesToForm();
    }

    public function create_content_object()
    {
        $contentObject = new CalendarEvent();

        $this->setCalendarEventProperties($contentObject);
        $this->setRecurrenceProperties($contentObject);

        $this->set_content_object($contentObject);

        return parent::create_content_object();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent $calendarEvent
     *
     * @return \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent
     */
    public function setCalendarEventProperties($calendarEvent)
    {
        $values = $this->exportValues();

        $calendarEvent->set_location($values[CalendarEvent::PROPERTY_LOCATION]);
        $calendarEvent->set_start_date(
            DatetimeUtilities::time_from_datepicker($values[CalendarEvent::PROPERTY_START_DATE])
        );
        $calendarEvent->set_end_date(
            DatetimeUtilities::time_from_datepicker($values[CalendarEvent::PROPERTY_END_DATE])
        );
    }

    /**
     * @param string[] $defaults
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = array())
    {
        $calendarEvent = $this->get_content_object();

        if (isset($calendarEvent) && $this->form_type == self::TYPE_EDIT)
        {
            $defaults[CalendarEvent::PROPERTY_LOCATION] = $calendarEvent->get_location();
            $defaults[CalendarEvent::PROPERTY_START_DATE] = $calendarEvent->get_start_date();
            $defaults[CalendarEvent::PROPERTY_END_DATE] = $calendarEvent->get_end_date();
        }
        else
        {
            $defaults[CalendarEvent::PROPERTY_START_DATE] = time();
            $defaults[CalendarEvent::PROPERTY_END_DATE] = time() + 3600;
        }

        parent::setDefaults($defaults);
    }

    /**
     * @return boolean
     */
    public function update_content_object()
    {
        $contentObject = $this->get_content_object();

        $this->setCalendarEventProperties($contentObject);
        $this->setRecurrenceProperties($contentObject);

        return parent::update_content_object();
    }
}
