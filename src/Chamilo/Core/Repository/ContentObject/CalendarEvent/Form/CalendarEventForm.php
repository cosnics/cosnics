<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Form;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Libraries\Calendar\Event\Recurrence\RecurringContentObjectForm;
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
        $translator = $this->getTranslator();

        $this->addElement(
            'category', $translator->trans('Properties', [], 'Chamilo\Core\Repository\ContentObject\CalendarEvent')
        );

        $this->add_datepicker(
            CalendarEvent::PROPERTY_START_DATE,
            $translator->trans('StartDate', [], 'Chamilo\Core\Repository\ContentObject\CalendarEvent'), true
        );
        $this->add_datepicker(
            CalendarEvent::PROPERTY_END_DATE,
            $translator->trans('EndDate', [], 'Chamilo\Core\Repository\ContentObject\CalendarEvent'), true
        );

        $this->addRule(
            CalendarEvent::PROPERTY_START_DATE,
            $translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES), 'required'
        );

        $this->addRule(
            CalendarEvent::PROPERTY_END_DATE,
            $translator->trans('ThisFieldIsRequired', [], Utilities::COMMON_LIBRARIES), 'required'
        );

        $this->addFrequencyPropertiesToForm();

        $this->add_textfield(
            CalendarEvent::PROPERTY_LOCATION,
            $translator->trans('Location', [], 'Chamilo\Core\Repository\ContentObject\CalendarEvent'), false
        );
    }

    /**
     * @param string[] $htmleditorOptions
     * @param boolean $inTab
     *
     * @throws \Exception
     */
    protected function build_creation_form($htmleditorOptions = [], $inTab = false)
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
    protected function build_editing_form($htmleditorOptions = [], $inTab = false)
    {
        parent::build_editing_form($htmleditorOptions, $inTab);
        $this->addCalendarEventPropertiesToForm();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function create_content_object()
    {
        $calendarEvent = new CalendarEvent();

        $this->setCalendarEventProperties($calendarEvent);
        $this->setRecurrenceProperties($calendarEvent);

        $this->set_content_object($calendarEvent);

        return parent::create_content_object();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent $calendarEvent
     *
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
     * @param mixed $filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        /**
         * @var \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent $calendarEvent
         */
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
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function update_content_object()
    {
        /**
         * @var \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent $calendarEvent
         */
        $calendarEvent = $this->get_content_object();

        $this->setCalendarEventProperties($calendarEvent);
        $this->setRecurrenceProperties($calendarEvent);

        return parent::update_content_object();
    }
}
