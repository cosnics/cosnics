<?php
namespace Chamilo\Libraries\Calendar\Event\Recurrence;

use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Form
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class RecurringContentObjectForm extends ContentObjectForm
{
    public const PARAM_DAILY = 'daily';
    public const PARAM_DAY = 'day';
    public const PARAM_MONTHLY = 'monthly';
    public const PARAM_OPTION = 'option';
    public const PARAM_RANGE = 'range';
    public const PARAM_RANK = 'rank';
    public const PARAM_WEEKLY = 'weekly';
    public const PARAM_YEARLY = 'yearly';

    /**
     * @param string $type
     */
    protected function addByDayByMonthDayOptionPropertiesToForm(string $type)
    {
        $this->addElement('html', '<div class="form-row col-md-12 col-lg-8">');

        $this->add_select(
            $type . '[' . self::PARAM_OPTION . ']', null, [
            RecurringContentObject::PROPERTY_BYDAY => RecurringContentObject::PROPERTY_BYDAY,
            RecurringContentObject::PROPERTY_BYMONTHDAY => RecurringContentObject::PROPERTY_BYMONTHDAY
        ], false, ['style' => 'display: none;']
        );

        $html = [];

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
     * @throws \ReflectionException
     */
    protected function addByDayPropertiesToForm(
        string $type, string $subElement = self::PARAM_DAY, string $addonLabel = null, bool $multiple = false
    )
    {
        $this->addElement('html', '<div class="form-row col-md-12 col-lg-8">');

        $attributes = [];
        $attributes['style'] = 'display: none;';

        if ($multiple)
        {
            $attributes['multiple'] = 'multiple';
        }

        $frequencyClass = 'frequency-' . $type . '-byday';
        $elementNameParts = [];

        $elementNameParts[] = $type;
        $elementNameParts[] = '[' . RecurringContentObject::PROPERTY_BYDAY . ']';

        if (!empty($subElement))
        {
            $elementNameParts[] = '[' . $subElement . ']';
            $frequencyClass .= '-' . $subElement;
        }

        $this->add_select(
            implode('', $elementNameParts), null, RecurringContentObject::get_byday_options(), false, $attributes
        );

        $html = [];

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
     * @throws \ReflectionException
     */
    protected function addByDayRankAndByDayPropertiesToForm(string $type)
    {
        $this->addElement('html', '<div class="row frequency-' . $type . '-byday">');

        $this->addByDayRankPropertiesToForm($type);
        $this->addByDayPropertiesToForm($type);

        $this->addElement('html', '</div>');
    }

    /**
     * @throws \ReflectionException
     */
    protected function addByDayRankPropertiesToForm(string $type)
    {
        $this->addElement('html', '<div class="form-row col-md-12 col-lg-4">');

        $this->add_select(
            $type . '[' . RecurringContentObject::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']', '',
            RecurringContentObject::get_rank_options(), false
        );

        $this->addElement('html', '</div>');
    }

    /**
     * @param string $type
     */
    protected function addByMonthDayPropertiesToForm(string $type)
    {
        $html = [];

        $html[] = '<div class="row frequency-' . $type . '-bymonthday">';

        $html[] = '<div class="form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            $type . '[' . RecurringContentObject::PROPERTY_BYMONTHDAY . ']', null,
            RecurringContentObject::get_bymonthday_options(), false,
            ['multiple' => 'multiple', 'style' => 'display: none;']
        );

        $html = [];

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
    protected function addDailyFrequencyPropertiesToForm()
    {
        $html = [];

        $html[] = '<div class="row frequency-option frequency-daily">';
        $html[] = '<div class="form-row col-sm-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(
            self::PARAM_DAILY . '[' . RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL . ']', null, false
        );
        $html[] = '<input name="daily[frequency_interval]" type="text" class="form-control">';

        $html = [];

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
        $html = [];

        $html[] = '<div class="form-row col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield($type . '[' . RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL . ']', null, false);

        $html = [];

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
        $html = [];

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

        $this->add_select(
            RecurringContentObject::PROPERTY_FREQUENCY, null, RecurringContentObject::get_frequency_options(), false
        );

        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addDailyFrequencyPropertiesToForm();
        $this->addWeeklyFrequencyPropertiesToForm();
        $this->addMonthlyFrequencyPropertiesToForm();
        $this->addYearyFrequencyPropertiesToForm();
        $this->addFrequencyRangePropertiesToForm();

        // Container END
        $html = [];

        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '<div class="form_feedback"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        $html[] = '<script>';
        $html[] = '(function ($) {';
        $html[] = '    $(document).ready(function () {';
        $html[] = '        $(".frequency").contentObjectRecurrenceForm({name: "test"});';
        $html[] = '    })';
        $html[] = '})(jQuery);';

        $html[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $html));
        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar', true) .
            'jquery.contentObjectRecurrenceForm.js'
        )
        );

        $this->setFrequencyElementTemplates();
    }

    /**
     * @throws \Exception
     */
    protected function addFrequencyRangePropertiesToForm()
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<div class="row">';

        $html[] = '<div class="form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_RANGE, null, [
            1 => $translator->trans('RecurrenceRangeNoEndDate', [], 'Chamilo\Libraries\Calendar'),
            2 => $translator->trans('RecurrenceRangeCreate', [], 'Chamilo\Libraries\Calendar'),
            3 => $translator->trans('RecurrenceRangeUntil', [], 'Chamilo\Libraries\Calendar')
        ], false, ['style' => 'display: none;']
        );

        $html = [];

        $html[] = '<div class="btn-group btn-group-justified frequency-range">';
        $html[] = '<a class="btn btn-default" data-value="1" data-range="frequency-range-none">' .
            $translator->trans('RecurrenceRangeNoEndDate', [], 'Chamilo\Libraries\Calendar') . '</a>';
        $html[] = '<a class="btn btn-default" data-value="2" data-range="frequency-range-count">Beperkt aantal</a>';
        $html[] = '<a class="btn btn-default" data-value="3" data-range="frequency-range-until">' .
            $translator->trans('RecurrenceRangeUntil', [], 'Chamilo\Libraries\Calendar') . '</a>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-range-count">';

        $html[] = '<div class="form-row col-md-12 col-lg-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Maak</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(RecurringContentObject::PROPERTY_FREQUENCY_COUNT, null, false);

        $html = [];

        $html[] = '<div class="input-group-addon">afspraken</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-range-until">';

        $html[] = '<div class="form-inline form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addElement(
            'datepicker', $this->getAttribute('name'), RecurringContentObject::PROPERTY_UNTIL, '',
            ['class' => RecurringContentObject::PROPERTY_UNTIL], true
        );

        $html = [];

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
        $html = [];

        $html[] = '<div class="frequency-option frequency-weekly">';
        $html[] = '<div class="row">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addFrequencyIntervalPropertiesToForm(self::PARAM_WEEKLY, 'weken');

        $this->addByDayPropertiesToForm(self::PARAM_WEEKLY, '', 'Op', true);

        $html = [];

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

        $html = [];

        $html[] = '<div class="row">';
        $html[] = '<div class="form-group col-md-12 col-lg-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">van de maand</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            self::PARAM_YEARLY . '[' . RecurringContentObject::PROPERTY_BYMONTH . ']', '',
            RecurringContentObject::get_bymonth_options()
        );

        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addElement('html', '</div>');
    }

    /**
     * @param string[] $defaults
     * @param mixed filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        /**
         * @var \Chamilo\Libraries\Calendar\Event\Recurrence\RecurringContentObject $recurringContentObject
         */

        $recurringContentObject = $this->get_content_object();

        if (isset($recurringContentObject) && $this->form_type == self::TYPE_EDIT)
        {
            $defaults[RecurringContentObject::PROPERTY_FREQUENCY] = $recurringContentObject->get_frequency();

            if ($recurringContentObject->has_frequency())
            {
                switch ($recurringContentObject->get_frequency())
                {
                    case 1 :
                        $defaults[self::PARAM_DAILY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] =
                            $recurringContentObject->get_frequency_interval();
                        break;
                    case 2 :
                        if ($recurringContentObject->get_byday() == 'MO,TU,WE,TH,FR' &&
                            $recurringContentObject->get_frequency_interval() == 1)
                        {
                            $defaults[RecurringContentObject::PROPERTY_FREQUENCY] = 3;
                            $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_BYDAY] = [1];
                        }
                        elseif ($recurringContentObject->get_frequency_interval() == 2 &&
                            $recurringContentObject->get_byday() == '')
                        {
                            $defaults[RecurringContentObject::PROPERTY_FREQUENCY] = 4;
                            $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_BYDAY] = [1];
                        }
                        else
                        {
                            $bydays = RecurringContentObject::get_byday_parts($recurringContentObject->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_BYDAY][] =
                                    RecurringContentObject::get_day_format(
                                        $byday[1]
                                    );
                            }
                            $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] =
                                $recurringContentObject->get_frequency_interval();
                        }
                        break;
                    case 5 :
                        $defaults[self::PARAM_MONTHLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] =
                            $recurringContentObject->get_frequency_interval();
                        if ($recurringContentObject->get_bymonthday())
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] =
                                RecurringContentObject::PROPERTY_BYMONTHDAY;
                            $defaults[self::PARAM_MONTHLY][RecurringContentObject::PROPERTY_BYMONTHDAY] =
                                $recurringContentObject->get_bymonthday();
                        }
                        else
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = RecurringContentObject::PROPERTY_BYDAY;
                            $bydays = RecurringContentObject::get_byday_parts($recurringContentObject->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_MONTHLY][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_DAY] =
                                    RecurringContentObject::get_day_format(
                                        $byday[1]
                                    );
                                $defaults[self::PARAM_MONTHLY][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_RANK] =
                                    $byday[0];
                            }
                        }
                        break;
                    case 6 :
                        $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] =
                            $recurringContentObject->get_frequency_interval();
                        if ($recurringContentObject->get_bymonthday())
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] =
                                RecurringContentObject::PROPERTY_BYMONTHDAY;
                            $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_BYMONTHDAY] =
                                $recurringContentObject->get_bymonthday();
                            $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_BYMONTH] =
                                $recurringContentObject->get_bymonth();
                        }
                        else
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = RecurringContentObject::PROPERTY_BYDAY;
                            $bydays = RecurringContentObject::get_byday_parts($recurringContentObject->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_DAY] =
                                    RecurringContentObject::get_day_format(
                                        $byday[1]
                                    );
                                $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_RANK] =
                                    $byday[0];
                                $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_BYMONTH] =
                                    $recurringContentObject->get_bymonth();
                            }
                        }
                        break;
                }

                if ($recurringContentObject->get_until() == 0)
                {
                    if ($recurringContentObject->get_frequency_count() > 0)
                    {
                        $defaults[RecurringContentObject::PROPERTY_FREQUENCY_COUNT] =
                            $recurringContentObject->get_frequency_count();
                        $defaults[self::PARAM_RANGE] = 2;
                    }
                    else
                    {
                        $defaults[self::PARAM_RANGE] = 1;
                        $defaults[RecurringContentObject::PROPERTY_UNTIL] = 0;
                        $defaults[RecurringContentObject::PROPERTY_FREQUENCY_COUNT] = 10;
                    }
                }
                else
                {
                    $defaults[self::PARAM_RANGE] = 3;
                    $defaults[RecurringContentObject::PROPERTY_UNTIL] = $recurringContentObject->get_until();
                    $defaults[RecurringContentObject::PROPERTY_FREQUENCY_COUNT] = 10;
                }
            }
        }
        else
        {
            $defaults[RecurringContentObject::PROPERTY_FREQUENCY] = 0;

            $defaults[self::PARAM_DAILY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] = 1;

            $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_WEEKLY][RecurringContentObject::PROPERTY_BYDAY] = [1];

            $defaults[self::PARAM_MONTHLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 0;
            $defaults[self::PARAM_MONTHLY][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
            $defaults[self::PARAM_MONTHLY][RecurringContentObject::PROPERTY_BYMONTHDAY] = [1];

            $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 0;
            $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
            $defaults[self::PARAM_YEARLY][RecurringContentObject::PROPERTY_BYMONTHDAY] = [1];

            $defaults[self::PARAM_RANGE] = 1;
            $defaults[RecurringContentObject::PROPERTY_UNTIL] = null;
            $defaults[RecurringContentObject::PROPERTY_FREQUENCY_COUNT] = 10;
        }

        parent::setDefaults($defaults);
    }

    protected function setFrequencyElementTemplates()
    {
        $elementNames = [
            RecurringContentObject::PROPERTY_FREQUENCY,

            self::PARAM_DAILY . '[' . RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL . ']',

            self::PARAM_WEEKLY . '[' . RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_WEEKLY . '[' . RecurringContentObject::PROPERTY_BYDAY . ']',

            self::PARAM_MONTHLY . '[' . RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_MONTHLY . '[' . self::PARAM_OPTION . ']',
            self::PARAM_MONTHLY . '[' . RecurringContentObject::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']',
            self::PARAM_MONTHLY . '[' . RecurringContentObject::PROPERTY_BYDAY . '][' . self::PARAM_DAY . ']',
            self::PARAM_MONTHLY . '[' . RecurringContentObject::PROPERTY_BYMONTHDAY . ']',

            self::PARAM_YEARLY . '[' . RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_YEARLY . '[' . self::PARAM_OPTION . ']',
            self::PARAM_YEARLY . '[' . RecurringContentObject::PROPERTY_BYMONTHDAY . ']',
            self::PARAM_YEARLY . '[' . RecurringContentObject::PROPERTY_BYDAY . ']' . '[' . self::PARAM_RANK . ']',
            self::PARAM_YEARLY . '[' . RecurringContentObject::PROPERTY_BYDAY . ']' . '[' . self::PARAM_DAY . ']',
            self::PARAM_YEARLY . '[' . RecurringContentObject::PROPERTY_BYMONTH . ']',

            self::PARAM_RANGE,
            RecurringContentObject::PROPERTY_FREQUENCY_COUNT,
            RecurringContentObject::PROPERTY_UNTIL

        ];

        foreach ($elementNames as $elementName)
        {
            $this->get_renderer()->setElementTemplate('{element}', $elementName);
        }
    }

    /**
     * @throws \Exception
     */
    public function setRecurrenceProperties(RecurringContentObject $calendarEvent)
    {
        $values = $this->exportValues();

        $frequency = $values[RecurringContentObject::PROPERTY_FREQUENCY];

        $calendarEvent->set_frequency($values[RecurringContentObject::PROPERTY_FREQUENCY]);

        if ($frequency == RecurringContentObject::FREQUENCY_NONE)
        {
            $calendarEvent->set_frequency(0);
            $calendarEvent->set_frequency_interval(null);
            $calendarEvent->set_frequency_count(0);
            $calendarEvent->set_until(0);
            $calendarEvent->set_byday(null);
            $calendarEvent->set_bymonthday(null);
            $calendarEvent->set_bymonth(null);
        }
        else
        {
            switch ($frequency)
            {
                case 1 :
                    $frequencyName = self::PARAM_DAILY;
                    $calendarEvent->set_byday(null);
                    $calendarEvent->set_bymonthday(null);
                    $calendarEvent->set_bymonth(null);

                    $calendarEvent->set_frequency_interval(
                        $values[$frequencyName][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL]
                    );
                    break;
                case 2 :
                    $frequencyName = self::PARAM_WEEKLY;
                    $bydays = [];
                    foreach ($values[$frequencyName][RecurringContentObject::PROPERTY_BYDAY] as $byday)
                    {
                        $bydays[] = RecurringContentObject::get_day_ical_format($byday);
                    }
                    $calendarEvent->set_byday(implode(',', $bydays));

                    $calendarEvent->set_bymonthday(null);
                    $calendarEvent->set_bymonth(null);

                    $calendarEvent->set_frequency_interval(
                        $values[$frequencyName][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL]
                    );
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
                    $frequencyName = self::PARAM_MONTHLY;
                    if ($values[$frequencyName][self::PARAM_OPTION] == RecurringContentObject::PROPERTY_BYDAY)
                    {
                        $calendarEvent->set_byday(
                            RecurringContentObject::get_byday_ical_format(
                                $values[$frequencyName][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_RANK],
                                $values[$frequencyName][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_DAY]
                            )
                        );

                        $calendarEvent->set_bymonthday(null);
                    }
                    else
                    {
                        $calendarEvent->set_bymonthday(
                            implode(',', $values[$frequencyName][RecurringContentObject::PROPERTY_BYMONTHDAY])
                        );
                        $calendarEvent->set_byday(null);
                    }

                    $calendarEvent->set_frequency_interval(
                        $values[$frequencyName][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL]
                    );

                    $calendarEvent->set_bymonth(null);
                    break;
                case 6 :
                    $frequencyName = self::PARAM_YEARLY;

                    if ($values[$frequencyName][self::PARAM_OPTION] == RecurringContentObject::PROPERTY_BYDAY)
                    {
                        $calendarEvent->set_byday(
                            RecurringContentObject::get_byday_ical_format(
                                $values[$frequencyName][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_RANK],
                                $values[$frequencyName][RecurringContentObject::PROPERTY_BYDAY][self::PARAM_DAY]
                            )
                        );

                        $calendarEvent->set_bymonth($values[$frequencyName][RecurringContentObject::PROPERTY_BYMONTH]);
                        $calendarEvent->set_bymonthday(null);
                    }
                    else
                    {
                        $calendarEvent->set_bymonthday(
                            implode(',', $values[$frequencyName][RecurringContentObject::PROPERTY_BYMONTHDAY])
                        );

                        $calendarEvent->set_bymonth($values[$frequencyName][RecurringContentObject::PROPERTY_BYMONTH]);
                        $calendarEvent->set_byday(null);
                    }

                    $calendarEvent->set_frequency_interval(
                        $values[$frequencyName][RecurringContentObject::PROPERTY_FREQUENCY_INTERVAL]
                    );

                    break;
            }

            switch ($values[self::PARAM_RANGE])
            {
                case 1:
                    $calendarEvent->set_until(0);
                    $calendarEvent->set_frequency_count(0);
                    break;
                case 2 :
                    $calendarEvent->set_frequency_count($values[RecurringContentObject::PROPERTY_FREQUENCY_COUNT]);
                    $calendarEvent->set_until(0);
                    break;
                case 3 :
                    $calendarEvent->set_frequency_count(0);
                    $calendarEvent->set_until(
                        DatetimeUtilities::getInstance()->timeFromDatepicker(
                            $values[RecurringContentObject::PROPERTY_UNTIL]
                        )
                    );
            }
        }
    }
}
