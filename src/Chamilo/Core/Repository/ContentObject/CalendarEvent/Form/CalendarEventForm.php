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
     * @param string $type
     */
    protected function addByDayByMonthDayOptionPropertiesToForm(string $type)
    {
        $this->addElement('html', '<div class="form-row col-md-12 col-lg-8">');

        $this->add_select(
            $type . '[' . self::PARAM_OPTION . ']', null, array(
            CalendarEvent::PROPERTY_BYDAY => CalendarEvent::PROPERTY_BYDAY,
            CalendarEvent::PROPERTY_BYMONTHDAY => CalendarEvent::PROPERTY_BYMONTHDAY
        ), false, array('style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-' . $type . '-option">';
        $html[] = '<a class="btn btn-default" data-option="frequency-' . $type .
            '-byday" data-value="byday">op deze weekdagen</a>';
        $html[] = '<a class="btn btn-default" data-option="frequency-' . $type .
            '-bymonthday" data-value="bymonthday">op deze dagen</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @param string $type
     * @param string $subElement
     * @param string $addonLabel
     * @param boolean $multiple
     */
    protected function addByDayPropertiesToForm(
        string $type, string $subElement = self::PARAM_DAY, string $addonLabel = null, bool $multiple = false
    )
    {
        $this->addElement('html', '<div class="form-row col-md-12 col-lg-8">');

        $attributes = array();
        $attributes['style'] = 'display: none;';

        if ($multiple)
        {
            $attributes['multiple'] = 'multiple';
        }

        $frequencyClass = 'frequency-' . $type . '-byday';
        $elementNameParts = array();

        $elementNameParts[] = $type;
        $elementNameParts[] = '[' . CalendarEvent::PROPERTY_BYDAY . ']';

        if (!empty($subElement))
        {
            $elementNameParts[] = '[' . $subElement . ']';
            $frequencyClass .= '-' . $subElement;
        }

        $this->add_select(implode('', $elementNameParts), null, CalendarEvent::get_byday_options(), false, $attributes);

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified ' . $frequencyClass . '">';

        if (!empty($addonLabel))
        {
            $html[] = '<span class="input-group-addon">' . $addonLabel . '</span>';
        }

        $html[] = '<a class="btn btn-default" data-value="1">Ma</a>';
        $html[] = '<a class="btn btn-default" data-value="2">Di</a>';
        $html[] = '<a class="btn btn-default" data-value="3">Wo</a>';
        $html[] = '<a class="btn btn-default" data-value="4">Do</a>';
        $html[] = '<a class="btn btn-default" data-value="5">Vr</a>';
        $html[] = '<a class="btn btn-default" data-value="6">Za</a>';
        $html[] = '<a class="btn btn-default" data-value="7">Zo</a>';
        $html[] = '</div>';

        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @param string $type
     */
    protected function addByDayRankAndByDayPropertiesToForm(string $type)
    {
        $this->addElement('html', '<div class="row frequency-' . $type . '-byday">');

        $this->addByDayRankPropertiesToForm($type);
        $this->addByDayPropertiesToForm($type);

        $this->addElement('html', '</div>');
    }

    /**
     * @param string $type
     */
    protected function addByDayRankPropertiesToForm(string $type)
    {
        $this->addElement('html', '<div class="form-row col-md-12 col-lg-4">');

        $this->add_select(
            $type . '[' . CalendarEvent::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']', '',
            CalendarEvent::get_rank_options(), false
        );

        $this->addElement('html', '</div>');
    }

    /**
     * @param string $type
     */
    protected function addByMonthDayPropertiesToForm(string $type)
    {
        $html = array();

        $html[] = '<div class="row frequency-' . $type . '-bymonthday">';

        $html[] = '<div class="form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            $type . '[' . CalendarEvent::PROPERTY_BYMONTHDAY . ']', null, CalendarEvent::get_bymonthday_options(),
            false, array('multiple' => 'multiple', 'style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default" data-value="1">1</a>';
        $html[] = '<a class="btn btn-default" data-value="2">2</a>';
        $html[] = '<a class="btn btn-default" data-value="3">3</a>';
        $html[] = '<a class="btn btn-default" data-value="4">4</a>';
        $html[] = '<a class="btn btn-default" data-value="5">5</a>';
        $html[] = '<a class="btn btn-default" data-value="6">6</a>';
        $html[] = '<a class="btn btn-default" data-value="7">7</a>';
        $html[] = '<a class="btn btn-default" data-value="8">8</a>';
        $html[] = '</div>';

        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default" data-value="9">9</a>';
        $html[] = '<a class="btn btn-default" data-value="10">10</a>';
        $html[] = '<a class="btn btn-default" data-value="11">11</a>';
        $html[] = '<a class="btn btn-default" data-value="12">12</a>';
        $html[] = '<a class="btn btn-default" data-value="13">13</a>';
        $html[] = '<a class="btn btn-default" data-value="14">14</a>';
        $html[] = '<a class="btn btn-default" data-value="15">15</a>';
        $html[] = '<a class="btn btn-default" data-value="16">16</a>';
        $html[] = '</div>';

        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default" data-value="17">17</a>';
        $html[] = '<a class="btn btn-default" data-value="18">18</a>';
        $html[] = '<a class="btn btn-default" data-value="19">19</a>';
        $html[] = '<a class="btn btn-default" data-value="20">20</a>';
        $html[] = '<a class="btn btn-default" data-value="21">21</a>';
        $html[] = '<a class="btn btn-default" data-value="22">22</a>';
        $html[] = '<a class="btn btn-default" data-value="23">23</a>';
        $html[] = '<a class="btn btn-default" data-value="24">24</a>';
        $html[] = '</div>';

        $html[] = '<div class="btn-group btn-group-justified">';
        $html[] = '<a class="btn btn-default" data-value="25">25</a>';
        $html[] = '<a class="btn btn-default" data-value="26">26</a>';
        $html[] = '<a class="btn btn-default" data-value="27">27</a>';
        $html[] = '<a class="btn btn-default" data-value="28">28</a>';
        $html[] = '<a class="btn btn-default" data-value="29">29</a>';
        $html[] = '<a class="btn btn-default" data-value="30">30</a>';
        $html[] = '<a class="btn btn-default" data-value="31">31</a>';
        $html[] = '<a class="btn btn-default" data-value="-1">Last</a>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

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
     * @throws \Exception
     */
    protected function addDailyFrequencyPropertiesToForm()
    {
        $html = array();

        $html[] = '<div class="row frequency-option frequency-daily">';
        $html[] = '<div class="form-row col-sm-12">';
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

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @param string $type
     * @param string $addonLabel
     *
     * @throws \Exception
     */
    protected function addFrequencyIntervalPropertiesToForm(string $type, string $addonLabel)
    {
        $html = array();

        $html[] = '<div class="form-row col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield($type . '[' . CalendarEvent::PROPERTY_FREQUENCY_INTERVAL . ']', null, false);

        $html = array();

        $html[] = '<div class="input-group-addon">' . $addonLabel . '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @throws \Exception
     */
    protected function addFrequencyPropertiesToForm()
    {
        $html = array();

        // Container START
        $html[] = '<div class="form-row row">';
        $html[] = '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 form-label control-label">Herhaling</div >';
        $html[] = '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 formw">';
        $html[] = '<div class="element">';

        $html[] = '<div class="frequency">';

        // Frequency
        $html[] = '<div class="row">';
        $html[] = '<div class="form-row col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(CalendarEvent::PROPERTY_FREQUENCY, null, CalendarEvent::get_frequency_options(), false);

        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addDailyFrequencyPropertiesToForm();
        $this->addWeeklyFrequencyPropertiesToForm();
        $this->addMonthlyFrequencyPropertiesToForm();
        $this->addYearyFrequencyPropertiesToForm();
        $this->addFrequencyRangePropertiesToForm();

        // Container END
        $html = array();

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
        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\CalendarEvent', true) .
            'jquery.calendarFrequency.js'
        )
        );

        $this->setFrequencyElementTemplates();
    }

    /**
     * @throws \Exception
     */
    protected function addFrequencyRangePropertiesToForm()
    {
        $html = array();

        $html[] = '<div class="row">';

        $html[] = '<div class="form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_RANGE, null,
            array(1 => Translation::get('NoEndDate'), 2 => Translation::get('Create'), 3 => Translation::get('Until')),
            false, array('style' => 'display: none;')
        );

        $html = array();

        $html[] = '<div class="btn-group btn-group-justified frequency-range">';
        $html[] = '<a class="btn btn-default" data-value="1" data-range="frequency-range-none">Geen einddatum</a>';
        $html[] = '<a class="btn btn-default" data-value="2" data-range="frequency-range-count">Beperkt aantal</a>';
        $html[] = '<a class="btn btn-default" data-value="3" data-range="frequency-range-until">Tot</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-range-count">';

        $html[] = '<div class="form-row col-md-12 col-lg-12">';
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

        $html[] = '<div class="form-inline form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addElement(
            'datepicker', $this->getAttribute('name'), CalendarEvent::PROPERTY_UNTIL, '',
            array('class' => CalendarEvent::PROPERTY_UNTIL), true
        );

        $html = array();

        $html[] = '</div>';

        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @throws \Exception
     */
    protected function addMonthlyFrequencyPropertiesToForm()
    {
        $this->addElement('html', '<div class="frequency-option frequency-monthly">');

        $this->addElement('html', '<div class="row">');

        $this->addFrequencyIntervalPropertiesToForm(self::PARAM_MONTHLY, 'maand');
        $this->addByDayByMonthDayOptionPropertiesToForm(self::PARAM_MONTHLY);

        $this->addElement('html', '</div>');

        $this->addByDayRankAndByDayPropertiesToForm(self::PARAM_MONTHLY);
        $this->addByMonthDayPropertiesToForm(self::PARAM_MONTHLY);

        $this->addElement('html', '</div>');
    }

    /**
     * @throws \Exception
     */
    protected function addWeeklyFrequencyPropertiesToForm()
    {
        $html = array();

        $html[] = '<div class="frequency-option frequency-weekly">';
        $html[] = '<div class="row">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addFrequencyIntervalPropertiesToForm(self::PARAM_WEEKLY, 'weken');

        $this->addByDayPropertiesToForm(self::PARAM_WEEKLY, '', 'Op', true);

        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @throws \Exception
     */
    protected function addYearyFrequencyPropertiesToForm()
    {
        $this->addElement('html', '<div class="frequency-option frequency-yearly">');

        $this->addElement('html', '<div class="row">');

        $this->addFrequencyIntervalPropertiesToForm(self::PARAM_YEARLY, 'jaar');
        $this->addByDayByMonthDayOptionPropertiesToForm(self::PARAM_YEARLY);

        $this->addElement('html', '</div>');

        $this->addByDayRankAndByDayPropertiesToForm(self::PARAM_YEARLY);
        $this->addByMonthDayPropertiesToForm(self::PARAM_YEARLY);

        $html = array();

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

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addElement('html', '</div>');
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
        $object = new CalendarEvent();
        $object = $this->setCalendarEventProperties($object);

        $this->set_content_object($object);

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
        $frequency = $values[CalendarEvent::PROPERTY_FREQUENCY];

        $calendarEvent->set_frequency($values[CalendarEvent::PROPERTY_FREQUENCY]);

        switch ($frequency)
        {
            case 0 :
                $calendarEvent->set_frequency(0);
                $calendarEvent->set_frequency_interval(null);
                $calendarEvent->set_frequency_count(0);
                $calendarEvent->set_until(0);
                $calendarEvent->set_byday(null);
                $calendarEvent->set_bymonthday(null);
                $calendarEvent->set_bymonth(null);
                break;
            case 1 :
                $frequency_type = self::PARAM_DAILY;
                $calendarEvent->set_byday(null);
                $calendarEvent->set_bymonthday(null);
                $calendarEvent->set_bymonth(null);
                break;
            case 2 :
                $frequency_type = self::PARAM_WEEKLY;
                $bydays = array();
                foreach ($values[$frequency_type][CalendarEvent::PROPERTY_BYDAY] as $byday)
                {
                    $bydays[] = CalendarEvent::get_day_ical_format($byday);
                }
                $calendarEvent->set_byday(implode(',', $bydays));
                $calendarEvent->set_bymonthday(null);
                $calendarEvent->set_bymonth(null);
                break;
            case 3 :
                $calendarEvent->set_byday('MO,TU,WE,TH,FR');
                $calendarEvent->set_frequency(2);
                $calendarEvent->set_frequency_interval(1);
                $calendarEvent->set_bymonthday(null);
                $calendarEvent->set_bymonth(null);
                break;
            case 4 :
                $calendarEvent->set_frequency(2);
                $calendarEvent->set_frequency_interval(2);
                $calendarEvent->set_byday(null);
                $calendarEvent->set_bymonthday(null);
                $calendarEvent->set_bymonth(null);
                break;
            case 5 :
                $frequency_type = self::PARAM_MONTHLY;
                if ($values[$frequency_type][self::PARAM_OPTION] == CalendarEvent::PROPERTY_BYDAY)
                {
                    $calendarEvent->set_byday(
                        CalendarEvent::get_byday_ical_format(
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK],
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_DAY]
                        )
                    );

                    $calendarEvent->set_bymonthday(null);
                }
                else
                {
                    $calendarEvent->set_bymonthday(
                        implode(',', $values[$frequency_type][CalendarEvent::PROPERTY_BYMONTHDAY])
                    );
                    $calendarEvent->set_byday(null);
                }
                $calendarEvent->set_bymonth(null);
                break;
            case 6 :
                $frequency_type = self::PARAM_YEARLY;

                if ($values[$frequency_type][self::PARAM_OPTION] == CalendarEvent::PROPERTY_BYDAY)
                {
                    $calendarEvent->set_byday(
                        CalendarEvent::get_byday_ical_format(
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK],
                            $values[$frequency_type][CalendarEvent::PROPERTY_BYDAY][self::PARAM_DAY]
                        )
                    );
                    $calendarEvent->set_bymonth($values[$frequency_type][CalendarEvent::PROPERTY_BYMONTH]);
                    $calendarEvent->set_bymonthday(null);
                }
                else
                {
                    $calendarEvent->set_bymonthday(
                        implode(',', $values[$frequency_type][CalendarEvent::PROPERTY_BYMONTHDAY])
                    );
                    $calendarEvent->set_bymonth($values[$frequency_type][CalendarEvent::PROPERTY_BYMONTH]);
                    $calendarEvent->set_byday(null);
                }

                break;
        }

        if (in_array($frequency, array(1, 2, 5, 6)))
        {
            $calendarEvent->set_frequency_interval(
                $values[$frequency_type][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL]
            );
        }

        switch ($values[self::PARAM_RANGE])
        {
            case 1:
                $calendarEvent->set_until(0);
                $calendarEvent->set_frequency_count(0);
                break;
            case 2 :
                $calendarEvent->set_frequency_count($values[CalendarEvent::PROPERTY_FREQUENCY_COUNT]);
                $calendarEvent->set_until(0);
                break;
            case 3 :
                $calendarEvent->set_frequency_count(0);
                $calendarEvent->set_until(
                    DatetimeUtilities::time_from_datepicker($values[CalendarEvent::PROPERTY_UNTIL])
                );
        }

        return $calendarEvent;
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
            $defaults[CalendarEvent::PROPERTY_FREQUENCY] = $calendarEvent->get_frequency();

            $repeats = $calendarEvent->has_frequency();
            if ($repeats)
            {
                switch ($calendarEvent->get_frequency())
                {
                    case 1 :
                        $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                            $calendarEvent->get_frequency_interval();
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        break;
                    case 2 :
                        if ($calendarEvent->get_byday() == 'MO,TU,WE,TH,FR' &&
                            $calendarEvent->get_frequency_interval() == 1)
                        {
                            $defaults[CalendarEvent::PROPERTY_FREQUENCY] = 3;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        }
                        elseif ($calendarEvent->get_frequency_interval() == 2 && $calendarEvent->get_byday() == '')
                        {
                            $defaults[CalendarEvent::PROPERTY_FREQUENCY] = 4;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array(1);
                        }
                        else
                        {
                            $bydays = CalendarEvent::get_byday_parts($calendarEvent->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY][] =
                                    CalendarEvent::get_day_format(
                                        $byday[1]
                                    );
                            }
                            $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                                $calendarEvent->get_frequency_interval();
                        }
                        $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array(1);
                        break;

                    case 5 :
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                            $calendarEvent->get_frequency_interval();
                        if ($calendarEvent->get_bymonthday())
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYMONTHDAY;
                            $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] =
                                $calendarEvent->get_bymonthday();
                        }
                        else
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                            $bydays = CalendarEvent::get_byday_parts($calendarEvent->get_byday());
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
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array();
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array();
                        break;
                    case 6 :
                        $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] =
                            $calendarEvent->get_frequency_interval();
                        if ($calendarEvent->get_bymonthday())
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYMONTHDAY;
                            $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTHDAY] =
                                $calendarEvent->get_bymonthday();
                            $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTH] =
                                $calendarEvent->get_bymonth();
                        }
                        else
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                            $bydays = CalendarEvent::get_byday_parts($calendarEvent->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_DAY] =
                                    CalendarEvent::get_day_format(
                                        $byday[1]
                                    );
                                $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] =
                                    $byday[0];
                                $defaults[self::PARAM_YEARLY][CalendarEvent::PROPERTY_BYMONTH] =
                                    $calendarEvent->get_bymonth();
                            }
                        }
                        $defaults[self::PARAM_DAILY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_WEEKLY][CalendarEvent::PROPERTY_BYDAY] = array();
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = CalendarEvent::PROPERTY_BYDAY;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
                        $defaults[self::PARAM_MONTHLY][CalendarEvent::PROPERTY_BYMONTHDAY] = array();
                        break;
                }

                if ($calendarEvent->get_until() == 0)
                {
                    if ($calendarEvent->get_frequency_count() > 0)
                    {
                        $defaults[CalendarEvent::PROPERTY_FREQUENCY_COUNT] = $calendarEvent->get_frequency_count();
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
                    $defaults[CalendarEvent::PROPERTY_UNTIL] = $calendarEvent->get_until();
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

    /**
     * @return boolean
     */
    public function update_content_object()
    {
        $this->setCalendarEventProperties($this->get_content_object());

        return parent::update_content_object();
    }

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
