<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Form;

use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Form
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class CalendarEventForm extends ContentObjectForm
{
    const PARAM_DAILY = 'daily';
    const PARAM_DAY = 'day';
    const PARAM_MONTHLY = 'monthly';
    const PARAM_OPTION = 'option';
    const PARAM_RANGE = 'range';
    const PARAM_RANK = 'rank';
    const PARAM_WEEKLY = 'weekly';
    const PARAM_YEARLY = 'yearly';

    /**
     * @throws \Exception
     */
    public function add_calendar_form()
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

        $html = array();

        // Container START
        $html[] = '<div class="form-row row">';
        $html[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label control-label">Herhaling</div >';
        $html[] = '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">';
        $html[] = '<div class="element">';

        $html[] = '<div class="frequency">';

        // Frequency
        $html[] = '<div class="row">';
        $html[] = '<div class="col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(CalendarEvent::PROPERTY_FREQUENCY, null, CalendarEvent::get_frequency_options(), false);

        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        // Frequency Options START
        $html[] = '<div class="frequency-options">';

        // Daily
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Daily</h4></div></div>';

        $html[] = '<div class="row frequency-daily">';
        $html[] = '<div class="form-group col-sm-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(self::PARAM_DAILY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']', null, false);
        $html[] = '<input name="daily[frequency_interval]" type="text" class="form-control">';

        $html = array();

        $html[] = '<div class="input-group-addon">dagen</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        // Weekly
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Weekly</h4></div></div>';

        $html[] = '<div class="row frequency-weekly">';
        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(self::PARAM_WEEKLY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']', null, false);

        $html = array();

        $html[] = '<div class="input-group-addon">weken</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_WEEKLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']', null, CalendarEvent::get_byday_options(),
            false, array('multiple' => 'true', 'style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-weekly-byday">';
        $html[] = '<span class="input-group-addon">Op</span>';
        $html[] = '<a class="btn btn-default">Ma</a>';
        $html[] = '<a class="btn btn-default btn-primary">Di</a>';
        $html[] = '<a class="btn btn-default">Wo</a>';
        $html[] = '<a class="btn btn-default">Do</a>';
        $html[] = '<a class="btn btn-default">Vr</a>';
        $html[] = '<a class="btn btn-default">Za</a>';
        $html[] = '<a class="btn btn-default">Zo</a>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        // Monthly
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Monthly</h4></div></div>';

        $html[] = '<div class="row frequency-monthly">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']', null, false);

        $html = array();

        $html[] = '<div class="input-group-addon">maand</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_MONTHLY . '[' . self::PARAM_OPTION . ']', null,
            array(CalendarEvent::PROPERTY_BYDAY, 1 => CalendarEvent::PROPERTY_BYMONTH), false,
            array('style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-monthly-option">';
        $html[] = '<a class="btn btn-default">op deze weekdagen</a>';
        $html[] = '<a class="btn btn-default btn-primary">op deze dagen</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-monthly-byday">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']', '',
            CalendarEvent::get_rank_options(), false
        );

        $html = array();

        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_DAY . ']', null,
            CalendarEvent::get_byday_options(), false, array('style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-monthly-byday-day">';
        $html[] = '<a class="btn btn-default">Ma</a>';
        $html[] = '<a class="btn btn-default">Di</a>';
        $html[] = '<a class="btn btn-default">Wo</a>';
        $html[] = '<a class="btn btn-default">Do</a>';
        $html[] = '<a class="btn btn-default btn-primary">Vr</a>';
        $html[] = '<a class="btn btn-default">Za</a>';
        $html[] = '<a class="btn btn-default">Zo</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-monthly-bymonthday">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYMONTHDAY . ']', null,
            CalendarEvent::get_bymonthday_options(), false, array('multiple' => 'true', 'style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default btn-primary">1</a>';
        $html[] = '<a class="btn btn-default">2</a>';
        $html[] = '<a class="btn btn-default">3</a>';
        $html[] = '<a class="btn btn-default">4</a>';
        $html[] = '<a class="btn btn-default">5</a>';
        $html[] = '<a class="btn btn-default">6</a>';
        $html[] = '<a class="btn btn-default">7</a>';
        $html[] = '<a class="btn btn-default">8</a>';
        $html[] = '</div>';
        $html[] = '<div class="btn-group btn-group-justified">';

        $html[] = '<a class="btn btn-default">9</a>';
        $html[] = '<a class="btn btn-default btn-primary">10</a>';
        $html[] = '<a class="btn btn-default">11</a>';
        $html[] = '<a class="btn btn-default">12</a>';
        $html[] = '<a class="btn btn-default">13</a>';
        $html[] = '<a class="btn btn-default">14</a>';
        $html[] = '<a class="btn btn-default">15</a>';
        $html[] = '<a class="btn btn-default">16</a>';
        $html[] = '</div>';
        $html[] = '<div class="btn-group btn-group-justified">';

        $html[] = '<a class="btn btn-default">17</a>';
        $html[] = '<a class="btn btn-default">18</a>';
        $html[] = '<a class="btn btn-default">19</a>';
        $html[] = '<a class="btn btn-default btn-primary">20</a>';
        $html[] = '<a class="btn btn-default">21</a>';
        $html[] = '<a class="btn btn-default">22</a>';
        $html[] = '<a class="btn btn-default btn-primary">23</a>';
        $html[] = '<a class="btn btn-default">24</a>';
        $html[] = '</div>';
        $html[] = '<div class="btn-group btn-group-justified">';

        $html[] = '<a class="btn btn-default">25</a>';
        $html[] = '<a class="btn btn-default">26</a>';
        $html[] = '<a class="btn btn-default">27</a>';
        $html[] = '<a class="btn btn-default">28</a>';
        $html[] = '<a class="btn btn-default">29</a>';
        $html[] = '<a class="btn btn-default btn-primary">30</a>';
        $html[] = '<a class="btn btn-default">31</a>';
        $html[] = '<a class="btn btn-default">Last</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        // Yearly
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Yearly</h4></div></div>';

        $html[] = '<div class="row frequency-yearly">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']', null, false);

        $html = array();

        $html[] = '<div class="input-group-addon">jaar</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_YEARLY . '[' . self::PARAM_OPTION . ']', null,
            array(CalendarEvent::PROPERTY_BYDAY, 1 => CalendarEvent::PROPERTY_BYMONTH), false,
            array('style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-yearly-option">';
        $html[] = '<a class="btn btn-default">op deze weekdagen</a>';
        $html[] = '<a class="btn btn-default btn-primary">op deze dagen</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-yearly-byday">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']', null,
            CalendarEvent::get_rank_options()
        );

        $html = array();

        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']' . '[' . self::PARAM_DAY . ']', null,
            CalendarEvent::get_byday_options(), false, array('style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-yearly-byday-day">';
        $html[] = '<a class="btn btn-default">Ma</a>';
        $html[] = '<a class="btn btn-default">Di</a>';
        $html[] = '<a class="btn btn-default">Wo</a>';
        $html[] = '<a class="btn btn-default">Do</a>';
        $html[] = '<a class="btn btn-default btn-primary">Vr</a>';
        $html[] = '<a class="btn btn-default">Za</a>';
        $html[] = '<a class="btn btn-default">Zo</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-yearly-bymonthday">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYMONTHDAY . ']', null,
            CalendarEvent::get_bymonthday_options(), false, array('multiple' => 'true', 'style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default btn-primary">1</a>';
        $html[] = '<a class="btn btn-default">2</a>';
        $html[] = '<a class="btn btn-default">3</a>';
        $html[] = '<a class="btn btn-default">4</a>';
        $html[] = '<a class="btn btn-default">5</a>';
        $html[] = '<a class="btn btn-default">6</a>';
        $html[] = '<a class="btn btn-default">7</a>';
        $html[] = '<a class="btn btn-default">8</a>';
        $html[] = '</div>';
        $html[] = '<div class="btn-group btn-group-justified">';

        $html[] = '<a class="btn btn-default">9</a>';
        $html[] = '<a class="btn btn-default btn-primary">10</a>';
        $html[] = '<a class="btn btn-default">11</a>';
        $html[] = '<a class="btn btn-default">12</a>';
        $html[] = '<a class="btn btn-default">13</a>';
        $html[] = '<a class="btn btn-default">14</a>';
        $html[] = '<a class="btn btn-default">15</a>';
        $html[] = '<a class="btn btn-default">16</a>';
        $html[] = '</div>';
        $html[] = '<div class="btn-group btn-group-justified">';

        $html[] = '<a class="btn btn-default">17</a>';
        $html[] = '<a class="btn btn-default">18</a>';
        $html[] = '<a class="btn btn-default">19</a>';
        $html[] = '<a class="btn btn-default btn-primary">20</a>';
        $html[] = '<a class="btn btn-default">21</a>';
        $html[] = '<a class="btn btn-default">22</a>';
        $html[] = '<a class="btn btn-default btn-primary">23</a>';
        $html[] = '<a class="btn btn-default">24</a>';
        $html[] = '</div>';
        $html[] = '<div class="btn-group btn-group-justified">';

        $html[] = '<a class="btn btn-default">25</a>';
        $html[] = '<a class="btn btn-default">26</a>';
        $html[] = '<a class="btn btn-default">27</a>';
        $html[] = '<a class="btn btn-default">28</a>';
        $html[] = '<a class="btn btn-default">29</a>';
        $html[] = '<a class="btn btn-default btn-primary">30</a>';
        $html[] = '<a class="btn btn-default">31</a>';
        $html[] = '<a class="btn btn-default" disabled="disabled"></a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">van de maand</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYMONTH . ']', '', CalendarEvent::get_bymonth_options()
        );

        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        // Frequency Options END
        $html[] = '</div>';

        //Until
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Until</h4></div></div>';

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_RANGE, null,
            array(1 => Translation::get('NoEndDate'), 2 => Translation::get('Create'), 3 => Translation::get('Until')),
            false, array('style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-range">';
        $html[] = '<a class="btn btn-default">Geen einddatum</a>';
        $html[] = '<a class="btn btn-default">Beperkt aantal</a>';
        $html[] = '<a class="btn btn-default btn-primary">Tot</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-range-count">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Maak</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(CalendarEvent::PROPERTY_FREQUENCY_COUNT, null, false);

        $html = array();

        $html[] = '<div class="input-group-addon">afspraken</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-range-until">';

        $html[] = '<div class="form-inline form-group col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addElement(
            'datepicker', $this->getAttribute('name'), CalendarEvent::PROPERTY_UNTIL, '',
            array('class' => CalendarEvent::PROPERTY_UNTIL), true
        );

        $html = array();

        $html[] = '</div>';

        $html[] = '</div>';

        // Container END
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '<div class="form_feedback"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        $html[] = '<script>';
        $html[] = '(function ($) {';
        $html[] = '    $(document).ready(function () {';
        $html[] = '        $(".frequency").calendarFrequency({name: "test"});';
        $html[] = '    })';
        $html[] = '})(jQuery);';

        $html[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(CalendarEvent::PROPERTY_LOCATION, Translation::get('Location'), false);

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\CalendarEvent', true) .
            'jquery.calendarFrequency.js'
        )
        );

        $this->setFrequencyElementTemplates();
    }

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->add_calendar_form();
    }

    // Inherited

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->add_calendar_form();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent $object
     *
     * @return mixed
     */
    public function configure_calendar_event($object)
    {
        $values = $this->exportValues();

        $object->set_location($values[CalendarEvent::PROPERTY_LOCATION]);
        $object->set_start_date(DatetimeUtilities::time_from_datepicker($values[CalendarEvent::PROPERTY_START_DATE]));
        $object->set_end_date(DatetimeUtilities::time_from_datepicker($values[CalendarEvent::PROPERTY_END_DATE]));
        $frequency = $values[CalendarEvent::PROPERTY_FREQUENCY];

        $object->set_frequency($values[CalendarEvent::PROPERTY_FREQUENCY]);

        switch ($frequency)
        {
            case 0 :
                $object->set_frequency(0);
                $object->set_frequency_interval(null);
                $object->set_frequency_count(0);
                $object->set_until(0);
                $object->set_byday(null);
                $object->set_bymonthday(null);
                $object->set_bymonth(null);
                break;
            case 1 :
                $frequency_type = self::PARAM_DAILY;
                $object->set_byday(null);
                $object->set_bymonthday(null);
                $object->set_bymonth(null);
                break;
            case 2 :
                $frequency_type = self::PARAM_WEEKLY;
                $bydays = array();
                foreach ($values[$frequency_type][CalendarEvent::PROPERTY_BYDAY] as $byday)
                {
                    $bydays[] = CalendarEvent::get_day_ical_format($byday);
                }
                $object->set_byday(implode(',', $bydays));
                $object->set_bymonthday(null);
                $object->set_bymonth(null);
                break;
            case 3 :
                $object->set_byday('MO,TU,WE,TH,FR');
                $object->set_frequency(2);
                $object->set_frequency_interval(1);
                $object->set_bymonthday(null);
                $object->set_bymonth(null);
                break;
            case 4 :
                $object->set_frequency(2);
                $object->set_frequency_interval(2);
                $object->set_byday(null);
                $object->set_bymonthday(null);
                $object->set_bymonth(null);
                break;
            case 5 :
                $frequency_type = self::PARAM_MONTHLY;
                if ($values[$frequency_type][self::PARAM_OPTION] == CalendarEvent::PROPERTY_BYDAY)
                {
                    $object->set_byday(
                        CalendarEvent::get_byday_ical_format(
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK],
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_DAY]
                        )
                    );

                    $object->set_bymonthday(null);
                }
                else
                {
                    $object->set_bymonthday(
                        implode(',', $values[$frequency_type][CalendarEvent::PROPERTY_BYMONTHDAY])
                    );
                    $object->set_byday(null);
                }
                $object->set_bymonth(null);
                break;
            case 6 :
                $frequency_type = self::PARAM_YEARLY;

                if ($values[$frequency_type][self::PARAM_OPTION] == CalendarEvent::PROPERTY_BYDAY)
                {
                    $object->set_byday(
                        CalendarEvent::get_byday_ical_format(
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK],
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_DAY]
                        )
                    );
                    $object->set_bymonth($values[$frequency_type][CalendarEvent::PROPERTY_BYMONTH]);
                    $object->set_bymonthday(null);
                }
                else
                {
                    $object->set_bymonthday(
                        implode(',', $values[$frequency_type][CalendarEvent::PROPERTY_BYMONTHDAY])
                    );
                    $object->set_bymonth($values[$frequency_type][CalendarEvent::PROPERTY_BYMONTH]);
                    $object->set_byday(null);
                }

                break;
        }

        if (in_array($frequency, array(1, 2, 5, 6)))
        {
            $object->set_frequency_interval($values[$frequency_type][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL]);
        }

        switch ($values[self::PARAM_RANGE])
        {
            case 1:
                $object->set_until(0);
                $object->set_frequency_count(0);
                break;
            case 2 :
                $object->set_frequency_count($values[CalendarEvent::PROPERTY_FREQUENCY_COUNT]);
                $object->set_until(0);
                break;
            case 3 :
                $object->set_frequency_count(0);
                $object->set_until(DatetimeUtilities::time_from_datepicker($values[CalendarEvent::PROPERTY_UNTIL]));
        }

        return $object;
    }

    public function create_content_object()
    {
        $object = new CalendarEvent();
        $object = $this->configure_calendar_event($object);

        $this->set_content_object($object);

        return parent::create_content_object();
    }

    // Inherited

    public function setDefaults($defaults = array())
    {
        $calendar_event = $this->get_content_object();

        if (isset($calendar_event) && $this->form_type == self::TYPE_EDIT)
        {
            $defaults[CalendarEvent::PROPERTY_LOCATION] = $calendar_event->get_location();
            $defaults[CalendarEvent::PROPERTY_START_DATE] = $calendar_event->get_start_date();
            $defaults[CalendarEvent::PROPERTY_END_DATE] = $calendar_event->get_end_date();
            $defaults[CalendarEvent::PROPERTY_FREQUENCY] = $calendar_event->get_frequency();

            $repeats = $calendar_event->has_frequency();
            if ($repeats)
            {
                switch ($calendar_event->get_frequency())
                {
                    case 1 :
                        $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                            $calendar_event->get_frequency_interval();
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        break;
                    case 2 :
                        if ($calendar_event->get_byday() == 'MO,TU,WE,TH,FR' &&
                            $calendar_event->get_frequency_interval() == 1)
                        {
                            $defaults[CalendarEvent::PROPERTY_FREQUENCY] = 3;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        }
                        elseif ($calendar_event->get_frequency_interval() == 2 && $calendar_event->get_byday() == '')
                        {
                            $defaults[CalendarEvent::PROPERTY_FREQUENCY] = 4;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        }
                        else
                        {
                            $bydays = CalendarEvent::get_byday_parts($calendar_event->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY][] =
                                    CalendarEvent::get_day_format(
                                        $byday[1]
                                    );
                            }
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                                $calendar_event->get_frequency_interval();
                        }
                        $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        break;

                    case 5 :
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                            $calendar_event->get_frequency_interval();
                        if ($calendar_event->get_bymonthday())
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 1;
                            $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] =
                                $calendar_event->get_bymonthday();
                        }
                        else
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 0;
                            $bydays = CalendarEvent::get_byday_parts($calendar_event->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_DAY] =
                                    CalendarEvent::get_day_format(
                                        $byday[1]
                                    );
                                $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] =
                                    $byday[0];
                            }
                        }
                        $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        break;
                    case 6 :
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                            $calendar_event->get_frequency_interval();
                        if ($calendar_event->get_bymonthday())
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 0;
                            $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] =
                                $calendar_event->get_bymonthday();
                            $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTH] =
                                $calendar_event->get_bymonth();
                        }
                        else
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 1;
                            $bydays = CalendarEvent::get_byday_parts($calendar_event->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_DAY] =
                                    CalendarEvent::get_day_format(
                                        $byday[1]
                                    );
                                $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] =
                                    $byday[0];
                                $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTH] =
                                    $calendar_event->get_bymonth();
                            }
                        }
                        $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        break;
                }

                if ($calendar_event->get_until() == 0)
                {
                    if ($calendar_event->get_frequency_count() > 0)
                    {
                        $defaults[CalendarEvent::PROPERTY_FREQUENCY_COUNT] = $calendar_event->get_frequency_count();
                        $defaults[self::PARAM_RANGE] = 2;
                    }
                    else
                    {
                        $defaults[self::PARAM_RANGE] = 1;
                        $defaults[CalendarEvent::PROPERTY_UNTIL] = 0;
                        $defaults[CalendarEvent::PROPERTY_FREQUENCY_COUNT] = 10;
                    }
                }
                else
                {
                    $defaults[self::PARAM_RANGE] = 3;
                    $defaults[CalendarEvent::PROPERTY_UNTIL] = $calendar_event->get_until();
                    $defaults[CalendarEvent::PROPERTY_FREQUENCY_COUNT] = 10;
                }
            }
        }
        else
        {
            $defaults[CalendarEvent::PROPERTY_FREQUENCY] = 0;

            $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;

            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);

            $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 0;
            $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
            $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);

            $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 0;
            $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
            $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);

            $defaults[self::PARAM_RANGE] = 1;
            $defaults[CalendarEvent::PROPERTY_UNTIL] = null;
            $defaults[CalendarEvent::PROPERTY_FREQUENCY_COUNT] = 10;

            $defaults[CalendarEvent::PROPERTY_START_DATE] = time();
            $defaults[CalendarEvent::PROPERTY_END_DATE] = time() + 3600;
        }

        parent::setDefaults($defaults);
    }

    protected function setFrequencyElementTemplates()
    {
        $elementNames = array(
            CalendarEvent::PROPERTY_FREQUENCY,

            self::PARAM_DAILY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']',

            self::PARAM_WEEKLY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_WEEKLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']',

            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_MONTHLY . '[' . self::PARAM_OPTION . ']',
            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']',
            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_DAY . ']',
            self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYMONTHDAY . ']',

            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_YEARLY . '[' . self::PARAM_OPTION . ']',
            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYMONTHDAY . ']',
            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']' . '[' . self::PARAM_RANK . ']',
            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']' . '[' . self::PARAM_DAY . ']',
            self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYMONTH . ']',

            self::PARAM_RANGE,
            CalendarEvent::PROPERTY_FREQUENCY_COUNT,
            CalendarEvent::PROPERTY_UNTIL

        );

        foreach ($elementNames as $elementName)
        {
            $this->get_renderer()->setElementTemplate('{element}', $elementName);
        }
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object = $this->configure_calendar_event($object);

        return parent::update_content_object();
    }

    // Inherited

    /**
     * Validates the frequency interval
     *
     * @param int $frequencyInterval
     *
     * @return bool
     */
    public function validateFrequencyInterval($frequencyInterval)
    {
        $frequencyInterval = (int) $frequencyInterval;
        if (!is_integer($frequencyInterval))
        {
            return false;
        }

        return $frequencyInterval > 0;
    }
}
