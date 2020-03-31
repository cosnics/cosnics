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
 *
 * @package repository.lib.content_object.calendar_event
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 * This class represents a form to create or update calendar events
 */
class CalendarEventForm extends ContentObjectForm
{
    const PARAM_DAILY = 'daily';

    const PARAM_DAY = 'day';

    const PARAM_FREQUENCY_RANGE = 'frequency_range';

    const PARAM_MONTHLY = 'monthly';

    const PARAM_OPTION = 'option';

    const PARAM_RANGE = 'range';

    const PARAM_RANK = 'rank';

    const PARAM_WEEKLY = 'weekly';

    const PARAM_YEARLY = 'yearly';

    // Inherited

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

        // Frequency
        $html[] = '<div class="row">';
        $html[] = '<div class="col-lg-12">';
        $html[] = '<select name="frequency" class="form-control">';
        $html[] = '<option>Geen</option>';
        $html[] = '<option>Elke weekdag</option>';
        $html[] = '<option>Tweewekelijks</option>';
        $html[] = '<option>Dagelijks</option>';
        $html[] = '<option>Wekelijks</option>';
        $html[] = '<option>Maandelijks</option>';
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';
        // Daily
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Daily</h4></div></div>';

        $html[] = '<div class="form-group row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';
        $html[] = '<input name="daily[frequency_interval]" type="text" class="form-control">';
        $html[] = '<div class="input-group-addon">dagen</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        // Weekly
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Weekly</h4></div></div>';

        $html[] = '<div class="row">';
        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';
        $html[] = '<input name="weekly[frequency_interval]" type="text" class="form-control">';
        $html[] = '<div class="input-group-addon">weken</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';
        $html[] = '<div class="btn-group btn-group-justified">';
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

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';
        $html[] = '<input name="monthly[frequency_interval]" type="text" class="form-control">';
        $html[] = '<div class="input-group-addon">maand</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';
        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default">op deze weekdagen</a>';
        $html[] = '<a class="btn btn-default btn-primary">op deze dagen</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<select name="frequency" class="form-control">';
        $html[] = '<option>elke</option>';
        $html[] = '<option>de 1e</option>';
        $html[] = '<option>de 2e</option>';
        $html[] = '<option>de 3e</option>';
        $html[] = '<option>de 4e</option>';
        $html[] = '<option>de 5e</option>';
        $html[] = '</select>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';
        $html[] = '<div class="btn-group btn-group-justified">';
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

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';
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

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';
        $html[] = '<input name="yearly[frequency_interval]" type="text" class="form-control">';
        $html[] = '<div class="input-group-addon">jaar</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';
        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default">op deze weekdagen</a>';
        $html[] = '<a class="btn btn-default btn-primary">op deze dagen</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-4">';
        $html[] = '<select name="frequency" class="form-control">';
        $html[] = '<option>elke</option>';
        $html[] = '<option>de 1e</option>';
        $html[] = '<option>de 2e</option>';
        $html[] = '<option>de 3e</option>';
        $html[] = '<option>de 4e</option>';
        $html[] = '<option>de 5e</option>';
        $html[] = '</select>';
        $html[] = '</div>';

        $html[] = '<div class="form-group col-md-12 col-lg-8">';
        $html[] = '<div class="btn-group btn-group-justified">';
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

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';
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
        $html[] = '<select name="frequency" class="form-control">';
        $html[] = '<option>Januari</option>';
        $html[] = '<option>Februari</option>';
        $html[] = '<option>Maart</option>';
        $html[] = '<option>April</option>';
        $html[] = '<option>Mei</option>';
        $html[] = '<option>Juni</option>';
        $html[] = '<option>Juli</option>';
        $html[] = '<option>Augustus</option>';
        $html[] = '<option>September</option>';
        $html[] = '<option>Oktober</option>';
        $html[] = '<option>November</option>';
        $html[] = '<option>December</option>';
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        //Until
        $html[] = '<div class="row"><div class="col-md-12 col-lg-6"><h4>Until</h4></div></div>';

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';
        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default">Geen einddatum</a>';
        $html[] = '<a class="btn btn-default">Beperkt aantal</a>';
        $html[] = '<a class="btn btn-default btn-primary">Tot</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row">';

        $html[] = '<div class="form-group col-md-12 col-lg-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Maak</div>';
        $html[] = '<input name="yearly[frequency_interval]" type="text" class="form-control">';
        $html[] = '<div class="input-group-addon">afspraken</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

//        $this->addElement('static', null, Translation::get('Frequency'), implode(PHP_EOL, $html));

        // frequency
        // no repeat
        $this->addElement(
            'radio', CalendarEvent::PROPERTY_FREQUENCY, Translation::get('Frequency'), Translation::get('NoRepeat'), 0
        );

        // daily
        $this->addElement('radio', CalendarEvent::PROPERTY_FREQUENCY, '', Translation::get('Daily'), 1);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_1" class="frequency">');
        $daily_elements = array();
        $daily_elements[] = $this->createElement('static', '', null, Translation::get('Every'));

        $daily_elements[] = $this->createElement(
            'text', CalendarEvent::PROPERTY_FREQUENCY_INTERVAL, '', array('style' => 'width:50px', 'maxlength' => 2)
        );
        $daily_elements[] = $this->createElement('static', '', null, Translation::get('Days'));
        $this->addGroup($daily_elements, self::PARAM_DAILY);

        $this->addElement('html', '</div>');

        $this->addGroupRule(
            self::PARAM_DAILY, array(
                CalendarEvent::PROPERTY_FREQUENCY_INTERVAL => array(
                    array(
                        Translation::get('ThisFieldShouldBeNumeric', null, Utilities::COMMON_LIBRARIES),
                        'callback',
                        array($this, 'validateFrequencyInterval')
                    )
                )
            )
        );

        // weekly
        $this->addElement('radio', CalendarEvent::PROPERTY_FREQUENCY, '', Translation::get('Weekly'), 2);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_2" class="frequency">');

        $weekly_elements = array();
        $weekly_elements[] = $this->createElement('static', '', null, Translation::get('Every'));
        $weekly_elements[] = $this->createElement(
            'text', CalendarEvent::PROPERTY_FREQUENCY_INTERVAL, '', array('style' => 'width:50px', 'maxlength' => 2)
        );
        $weekly_elements[] = $this->createElement('static', '', null, Translation::get('Weeks'));
        $this->addGroup($weekly_elements, self::PARAM_WEEKLY);

        $this->addGroupRule(
            self::PARAM_WEEKLY, array(
                CalendarEvent::PROPERTY_FREQUENCY_INTERVAL => array(
                    array(
                        Translation::get('ThisFieldShouldBeNumeric', null, Utilities::COMMON_LIBRARIES),
                        'callback',
                        array($this, 'validateFrequencyInterval')
                    )
                )
            )
        );

        $this->addElement(
            'select', self::PARAM_WEEKLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']', '',
            CalendarEvent::get_byday_options(), 'multiple="true"'
        );
        $this->addElement('html', '</div>');

        //
        $this->addElement('radio', CalendarEvent::PROPERTY_FREQUENCY, '', Translation::get('EveryWeekday'), 3);
        //
        $this->addElement('radio', CalendarEvent::PROPERTY_FREQUENCY, '', Translation::get('BiWeekly'), 4);

        // monthly
        $this->addElement('radio', CalendarEvent::PROPERTY_FREQUENCY, '', Translation::get('Monthly'), 5);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_5" class="frequency">');

        $monthly_elements = array();
        $monthly_elements[] = $this->createElement('static', '', null, Translation::get('Every'));
        $monthly_elements[] = $this->createElement(
            'text', CalendarEvent::PROPERTY_FREQUENCY_INTERVAL, '', array('style' => 'width:50px', 'maxlength' => 2)
        );
        $monthly_elements[] = $this->createElement('static', '', null, Translation::get('Months'));
        $this->addGroup($monthly_elements, self::PARAM_MONTHLY);

        $this->addGroupRule(
            self::PARAM_MONTHLY, array(
                CalendarEvent::PROPERTY_FREQUENCY_INTERVAL => array(
                    array(
                        Translation::get('ThisFieldShouldBeNumeric', null, Utilities::COMMON_LIBRARIES),
                        'callback',
                        array($this, 'validateFrequencyInterval')
                    )
                )
            )
        );

        $monthly_byday_elements = array();
        $monthly_byday_elements[] = $this->createElement(
            'radio', self::PARAM_MONTHLY . '[' . self::PARAM_OPTION . ']', '', '', 0
        );
        $monthly_byday_elements[] = $this->createElement(
            'select', self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']', '',
            CalendarEvent::get_rank_options()
        );
        $monthly_byday_elements[] = $this->createElement(
            'select', self::PARAM_MONTHLY . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_DAY . ']', '',
            CalendarEvent::get_byday_options()
        );
        $this->addGroup($monthly_byday_elements);

        $monthly_bymonthday_elements = array();
        $monthly_bymonthday_elements[] = $this->createElement('radio', self::PARAM_OPTION, '', '', 1);
        $monthly_bymonthday_elements[] = $this->createElement('static', '', null, Translation::get('RecurOnDays'));
        $monthly_bymonthday_elements[] = $this->createElement(
            'select', CalendarEvent::PROPERTY_BYMONTHDAY, '', CalendarEvent::get_bymonthday_options(), 'multiple="true"'
        );

        $this->addGroup($monthly_bymonthday_elements, self::PARAM_MONTHLY);
        $this->addElement('html', '</div>');

        // yearly
        $this->addElement('radio', CalendarEvent::PROPERTY_FREQUENCY, '', Translation::get('Yearly'), 6);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_6" class="frequency">');

        $yearly_elements = array();
        $yearly_elements[] = $this->createElement('static', '', null, Translation::get('Every'));
        $yearly_elements[] = $this->createElement(
            'text', CalendarEvent::PROPERTY_FREQUENCY_INTERVAL, '', array('style' => 'width:50px', 'maxlength' => 2)
        );
        $yearly_elements[] = $this->createElement('static', '', null, Translation::get('Years'));
        $this->addGroup($yearly_elements, self::PARAM_YEARLY);

        $this->addGroupRule(
            self::PARAM_YEARLY, array(
                CalendarEvent::PROPERTY_FREQUENCY_INTERVAL => array(
                    array(
                        Translation::get('ThisFieldShouldBeNumeric', null, Utilities::COMMON_LIBRARIES),
                        'callback',
                        array($this, 'validateFrequencyInterval')
                    )
                )
            )
        );

        $yearly_bymonthday_elements = array();
        $yearly_bymonthday_elements[] = $this->createElement('radio', self::PARAM_OPTION, '', '', 0);
        $yearly_bymonthday_elements[] = $this->createElement('static', '', null, Translation::get('Every'));
        $yearly_bymonthday_elements[] = $this->createElement(
            'select', CalendarEvent::PROPERTY_BYMONTHDAY, '', CalendarEvent::get_bymonthday_options(), 'multiple="true"'
        );
        $this->addGroup($yearly_bymonthday_elements, self::PARAM_YEARLY);

        $yearly_byday_elements = array();
        $yearly_byday_elements[] = $this->createElement(
            'radio', self::PARAM_YEARLY . '[' . self::PARAM_OPTION . ']', '', '', 1
        );
        $yearly_byday_elements[] = $this->createElement(
            'select', self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']' . '[' . self::PARAM_RANK . ']', '',
            CalendarEvent::get_rank_options()
        );
        $yearly_byday_elements[] = $this->createElement(
            'select', self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYDAY . ']' . '[' . self::PARAM_DAY . ']', '',
            CalendarEvent::get_byday_options()
        );
        $this->addGroup($yearly_byday_elements);

        $yearly_month = array();
        $yearly_month[] = $this->createElement('static', '', null, Translation::get('Of'));
        $yearly_month[] = $this->createElement(
            'select', self::PARAM_YEARLY . '[' . CalendarEvent::PROPERTY_BYMONTH . ']', '',
            CalendarEvent::get_bymonth_options()
        );
        $this->addGroup($yearly_month);
        $this->addElement('html', '</div>');

        // range
        $this->addElement('html', '<div class="range">');
        $this->addElement('radio', self::PARAM_RANGE, Translation::get('Range'), Translation::get('NoEndDate'), 1);

        $interval_elements = array();
        $interval_elements[] = $this->createElement('radio', self::PARAM_RANGE, '', Translation::get('Create'), 2);
        $interval_elements[] = $this->createElement(
            'text', CalendarEvent::PROPERTY_FREQUENCY_COUNT, '', array('style' => 'width:50px', 'maxlength' => 2)
        );
        $interval_elements[] = $this->createElement('static', null, null, Translation::get('Appointments'));
        $this->addGroup($interval_elements, '', '', null, false);

        $until_elements = array();
        $until_elements[] = $this->createElement('radio', self::PARAM_RANGE, '', Translation::get('Until'), 3);
        $until_elements[] = $this->createElement(
            'datepicker', CalendarEvent::PROPERTY_UNTIL, '',
            array('form_name' => $this->getAttribute('name'), 'class' => CalendarEvent::PROPERTY_UNTIL), true
        );
        $this->addGroup($until_elements, 'until_elements', '', null, false);
        $this->addElement('html', '</div>');

        $this->add_textfield(CalendarEvent::PROPERTY_LOCATION, Translation::get('Location'), false);

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\CalendarEvent', true) .
            'Dates.min.js'
        )
        );
    }

    // Inherited

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->add_calendar_form();
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->add_calendar_form();
    }

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
                if ($values[$frequency_type][self::PARAM_OPTION] == 0)
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

                if ($values[$frequency_type][self::PARAM_OPTION] == 0)
                {
                    $object->set_bymonthday(
                        implode(',', $values[$frequency_type][CalendarEvent::PROPERTY_BYMONTHDAY])
                    );
                    $object->set_bymonth($values[$frequency_type][CalendarEvent::PROPERTY_BYMONTH]);
                    $object->set_byday(null);
                }
                else
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

                break;
        }

        if (in_array($frequency, array(1, 2, 5, 6)))
        {
            $object->set_frequency_interval($values[$frequency_type][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL]);
        }

        switch ($values[self::PARAM_RANGE])
        {
            case 1 :
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

    // Inherited

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

            $defaults[CalendarEvent :: PROPERTY_START_DATE] = time();
            $defaults[CalendarEvent :: PROPERTY_END_DATE] = time() + 3600;
        }

        parent::setDefaults($defaults);
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
