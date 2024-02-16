<?php
namespace Chamilo\Libraries\Calendar\Form;

use Chamilo\Libraries\Calendar\Event\RecurringContentObjectInterface;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\CalendarEvent\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 */
trait RecurringContentObjectFormTrait
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
     *
     * @throws \QuickformException
     */
    protected function addByDayByMonthDayOptionPropertiesToForm(string $type): void
    {
        $contentObjectClassName = $this->getContentObjectType();

        $this->addElement('html', '<div class="form-row col-md-12 col-lg-8">');

        $this->add_select(
            $type . '[' . self::PARAM_OPTION . ']', null, [
            $contentObjectClassName::PROPERTY_BYDAY => $contentObjectClassName::PROPERTY_BYDAY,
            $contentObjectClassName::PROPERTY_BYMONTHDAY => $contentObjectClassName::PROPERTY_BYMONTHDAY
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
     * @throws \QuickformException
     */
    protected function addByDayPropertiesToForm(
        string $type, string $subElement = self::PARAM_DAY, string $addonLabel = null, bool $multiple = false
    ): void
    {
        $contentObjectClassName = $this->getContentObjectType();

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
        $elementNameParts[] = '[' . $contentObjectClassName::PROPERTY_BYDAY . ']';

        if (!empty($subElement))
        {
            $elementNameParts[] = '[' . $subElement . ']';
            $frequencyClass .= '-' . $subElement;
        }

        $this->add_select(
            implode('', $elementNameParts), null, $contentObjectClassName::get_byday_options(), false, $attributes
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
     * @throws \QuickformException
     */
    protected function addByDayRankAndByDayPropertiesToForm(string $type): void
    {
        $this->addElement('html', '<div class="row frequency-' . $type . '-byday">');

        $this->addByDayRankPropertiesToForm($type);
        $this->addByDayPropertiesToForm($type);

        $this->addElement('html', '</div>');
    }

    /**
     * @throws \QuickformException
     */
    protected function addByDayRankPropertiesToForm(string $type): void
    {
        $contentObjectClassName = $this->getContentObjectType();

        $this->addElement('html', '<div class="form-row col-md-12 col-lg-4">');

        $this->add_select(
            $type . '[' . $contentObjectClassName::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']', '',
            $contentObjectClassName::get_rank_options(), false
        );

        $this->addElement('html', '</div>');
    }

    /**
     * @throws \QuickformException
     */
    protected function addByMonthDayPropertiesToForm(string $type): void
    {
        $contentObjectClassName = $this->getContentObjectType();

        $html = [];

        $html[] = '<div class="row frequency-' . $type . '-bymonthday">';

        $html[] = '<div class="form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_select(
            $type . '[' . $contentObjectClassName::PROPERTY_BYMONTHDAY . ']', null,
            $contentObjectClassName::get_bymonthday_options(), false,
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
    protected function addDailyFrequencyPropertiesToForm(): void
    {
        $contentObjectClassName = $this->getContentObjectType();

        $html = [];

        $html[] = '<div class="row frequency-option frequency-daily">';
        $html[] = '<div class="form-row col-sm-12">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield(
            self::PARAM_DAILY . '[' . $contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL . ']', null, false
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
    protected function addFrequencyIntervalPropertiesToForm(string $type, string $addonLabel): void
    {
        $contentObjectClassName = $this->getContentObjectType();

        $html = [];

        $html[] = '<div class="form-row col-md-12 col-lg-4">';
        $html[] = '<div class="input-group">';
        $html[] = '<div class="input-group-addon">Elke</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->add_textfield($type . '[' . $contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL . ']', null, false);

        $html = [];

        $html[] = '<div class="input-group-addon">' . $addonLabel . '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @throws \Exception
     */
    protected function addFrequencyPropertiesToForm(): void
    {
        $contentObjectClassName = $this->getContentObjectType();

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
            $contentObjectClassName::PROPERTY_FREQUENCY, null, $contentObjectClassName::get_frequency_options(), false
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
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Libraries\Calendar') .
            'jquery.contentObjectRecurrenceForm.js'
        )
        );

        $this->setFrequencyElementTemplates();
    }

    /**
     * @throws \Exception
     */
    protected function addFrequencyRangePropertiesToForm(): void
    {
        $contentObjectClassName = $this->getContentObjectType();
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

        $this->add_textfield($contentObjectClassName::PROPERTY_FREQUENCY_COUNT, null, false);

        $html = [];

        $html[] = '<div class="input-group-addon">afspraken</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '<div class="row frequency-range-until">';

        $html[] = '<div class="form-inline form-row col-md-12 col-lg-12">';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addElement(
            'datepicker', $this->getAttribute('name'), $contentObjectClassName::PROPERTY_UNTIL, '',
            ['class' => $contentObjectClassName::PROPERTY_UNTIL], true
        );

        $html = [];

        $html[] = '</div>';

        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @throws \Exception
     */
    protected function addMonthlyFrequencyPropertiesToForm(): void
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
    protected function addWeeklyFrequencyPropertiesToForm(): void
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
    protected function addYearyFrequencyPropertiesToForm(): void
    {
        $contentObjectClassName = $this->getContentObjectType();

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
            self::PARAM_YEARLY . '[' . $contentObjectClassName::PROPERTY_BYMONTH . ']', '',
            $contentObjectClassName::get_bymonth_options()
        );

        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));

        $this->addElement('html', '</div>');
    }

    abstract public function getContentObjectType(): string;

    /**
     * @param string[] $defaults
     * @param mixed $filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        $contentObjectClassName = $this->getContentObjectType();

        /**
         * @var \Chamilo\Libraries\Calendar\Event\RecurringContentObjectInterface $recurringContentObject
         */
        $recurringContentObject = $this->get_content_object();

        if (isset($recurringContentObject) && $this->form_type == self::TYPE_EDIT)
        {
            $defaults[$contentObjectClassName::PROPERTY_FREQUENCY] = $recurringContentObject->get_frequency();

            if ($recurringContentObject->has_frequency())
            {
                switch ($recurringContentObject->get_frequency())
                {
                    case 1 :
                        $defaults[self::PARAM_DAILY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] =
                            $recurringContentObject->get_frequency_interval();
                        break;
                    case 2 :
                        if ($recurringContentObject->get_byday() == 'MO,TU,WE,TH,FR' &&
                            $recurringContentObject->get_frequency_interval() == 1)
                        {
                            $defaults[$contentObjectClassName::PROPERTY_FREQUENCY] = 3;
                            $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_BYDAY] = [1];
                        }
                        elseif ($recurringContentObject->get_frequency_interval() == 2 &&
                            $recurringContentObject->get_byday() == '')
                        {
                            $defaults[$contentObjectClassName::PROPERTY_FREQUENCY] = 4;
                            $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_BYDAY] = [1];
                        }
                        else
                        {
                            $bydays = $contentObjectClassName::get_byday_parts($recurringContentObject->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_BYDAY][] =
                                    $contentObjectClassName::get_day_format(
                                        $byday[1]
                                    );
                            }
                            $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] =
                                $recurringContentObject->get_frequency_interval();
                        }
                        break;
                    case 5 :
                        $defaults[self::PARAM_MONTHLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] =
                            $recurringContentObject->get_frequency_interval();
                        if ($recurringContentObject->get_bymonthday())
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] =
                                $contentObjectClassName::PROPERTY_BYMONTHDAY;
                            $defaults[self::PARAM_MONTHLY][$contentObjectClassName::PROPERTY_BYMONTHDAY] =
                                $recurringContentObject->get_bymonthday();
                        }
                        else
                        {
                            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] =
                                $contentObjectClassName::PROPERTY_BYDAY;
                            $bydays = $contentObjectClassName::get_byday_parts($recurringContentObject->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_MONTHLY][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_DAY] =
                                    $contentObjectClassName::get_day_format(
                                        $byday[1]
                                    );
                                $defaults[self::PARAM_MONTHLY][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_RANK] =
                                    $byday[0];
                            }
                        }
                        break;
                    case 6 :
                        $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] =
                            $recurringContentObject->get_frequency_interval();
                        if ($recurringContentObject->get_bymonthday())
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] =
                                $contentObjectClassName::PROPERTY_BYMONTHDAY;
                            $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_BYMONTHDAY] =
                                $recurringContentObject->get_bymonthday();
                            $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_BYMONTH] =
                                $recurringContentObject->get_bymonth();
                        }
                        else
                        {
                            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = $contentObjectClassName::PROPERTY_BYDAY;
                            $bydays = $contentObjectClassName::get_byday_parts($contentObjectClassName->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_DAY] =
                                    $contentObjectClassName::get_day_format(
                                        $byday[1]
                                    );
                                $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_RANK] =
                                    $byday[0];
                                $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_BYMONTH] =
                                    $recurringContentObject->get_bymonth();
                            }
                        }
                        break;
                }

                if ($recurringContentObject->get_until() == 0)
                {
                    if ($recurringContentObject->get_frequency_count() > 0)
                    {
                        $defaults[$contentObjectClassName::PROPERTY_FREQUENCY_COUNT] =
                            $recurringContentObject->get_frequency_count();
                        $defaults[self::PARAM_RANGE] = 2;
                    }
                    else
                    {
                        $defaults[self::PARAM_RANGE] = 1;
                        $defaults[$contentObjectClassName::PROPERTY_UNTIL] = 0;
                        $defaults[$contentObjectClassName::PROPERTY_FREQUENCY_COUNT] = 10;
                    }
                }
                else
                {
                    $defaults[self::PARAM_RANGE] = 3;
                    $defaults[$contentObjectClassName::PROPERTY_UNTIL] = $recurringContentObject->get_until();
                    $defaults[$contentObjectClassName::PROPERTY_FREQUENCY_COUNT] = 10;
                }
            }
        }
        else
        {
            $defaults[$contentObjectClassName::PROPERTY_FREQUENCY] = 0;

            $defaults[self::PARAM_DAILY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] = 1;

            $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_WEEKLY][$contentObjectClassName::PROPERTY_BYDAY] = [1];

            $defaults[self::PARAM_MONTHLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_MONTHLY][self::PARAM_OPTION] = 0;
            $defaults[self::PARAM_MONTHLY][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
            $defaults[self::PARAM_MONTHLY][$contentObjectClassName::PROPERTY_BYMONTHDAY] = [1];

            $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self::PARAM_YEARLY][self::PARAM_OPTION] = 0;
            $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_RANK] = 0;
            $defaults[self::PARAM_YEARLY][$contentObjectClassName::PROPERTY_BYMONTHDAY] = [1];

            $defaults[self::PARAM_RANGE] = 1;
            $defaults[$contentObjectClassName::PROPERTY_UNTIL] = null;
            $defaults[$contentObjectClassName::PROPERTY_FREQUENCY_COUNT] = 10;
        }

        parent::setDefaults($defaults);
    }

    protected function setFrequencyElementTemplates(): void
    {
        $contentObjectClassName = $this->getContentObjectType();

        $elementNames = [
            $contentObjectClassName::PROPERTY_FREQUENCY,

            self::PARAM_DAILY . '[' . $contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL . ']',

            self::PARAM_WEEKLY . '[' . $contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_WEEKLY . '[' . $contentObjectClassName::PROPERTY_BYDAY . ']',

            self::PARAM_MONTHLY . '[' . $contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_MONTHLY . '[' . self::PARAM_OPTION . ']',
            self::PARAM_MONTHLY . '[' . $contentObjectClassName::PROPERTY_BYDAY . '][' . self::PARAM_RANK . ']',
            self::PARAM_MONTHLY . '[' . $contentObjectClassName::PROPERTY_BYDAY . '][' . self::PARAM_DAY . ']',
            self::PARAM_MONTHLY . '[' . $contentObjectClassName::PROPERTY_BYMONTHDAY . ']',

            self::PARAM_YEARLY . '[' . $contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL . ']',
            self::PARAM_YEARLY . '[' . self::PARAM_OPTION . ']',
            self::PARAM_YEARLY . '[' . $contentObjectClassName::PROPERTY_BYMONTHDAY . ']',
            self::PARAM_YEARLY . '[' . $contentObjectClassName::PROPERTY_BYDAY . ']' . '[' . self::PARAM_RANK . ']',
            self::PARAM_YEARLY . '[' . $contentObjectClassName::PROPERTY_BYDAY . ']' . '[' . self::PARAM_DAY . ']',
            self::PARAM_YEARLY . '[' . $contentObjectClassName::PROPERTY_BYMONTH . ']',

            self::PARAM_RANGE,
            $contentObjectClassName::PROPERTY_FREQUENCY_COUNT,
            $contentObjectClassName::PROPERTY_UNTIL

        ];

        foreach ($elementNames as $elementName)
        {
            $this->get_renderer()->setElementTemplate('{element}', $elementName);
        }
    }

    /**
     * @throws \Exception
     */
    public function setRecurrenceProperties(RecurringContentObjectInterface $calendarEvent): void
    {
        $contentObjectClassName = $this->getContentObjectType();
        $values = $this->exportValues();

        $frequency = $values[$contentObjectClassName::PROPERTY_FREQUENCY];

        $calendarEvent->set_frequency($values[$contentObjectClassName::PROPERTY_FREQUENCY]);

        if ($frequency == $contentObjectClassName::FREQUENCY_NONE)
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
                        $values[$frequencyName][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL]
                    );
                    break;
                case 2 :
                    $frequencyName = self::PARAM_WEEKLY;
                    $bydays = [];
                    foreach ($values[$frequencyName][$contentObjectClassName::PROPERTY_BYDAY] as $byday)
                    {
                        $bydays[] = $contentObjectClassName::get_day_ical_format($byday);
                    }
                    $calendarEvent->set_byday(implode(',', $bydays));

                    $calendarEvent->set_bymonthday(null);
                    $calendarEvent->set_bymonth(null);

                    $calendarEvent->set_frequency_interval(
                        $values[$frequencyName][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL]
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
                    if ($values[$frequencyName][self::PARAM_OPTION] == $contentObjectClassName::PROPERTY_BYDAY)
                    {
                        $calendarEvent->set_byday(
                            $contentObjectClassName::get_byday_ical_format(
                                $values[$frequencyName][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_RANK],
                                $values[$frequencyName][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_DAY]
                            )
                        );

                        $calendarEvent->set_bymonthday(null);
                    }
                    else
                    {
                        $calendarEvent->set_bymonthday(
                            implode(',', $values[$frequencyName][$contentObjectClassName::PROPERTY_BYMONTHDAY])
                        );
                        $calendarEvent->set_byday(null);
                    }

                    $calendarEvent->set_frequency_interval(
                        $values[$frequencyName][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL]
                    );

                    $calendarEvent->set_bymonth(null);
                    break;
                case 6 :
                    $frequencyName = self::PARAM_YEARLY;

                    if ($values[$frequencyName][self::PARAM_OPTION] == $contentObjectClassName::PROPERTY_BYDAY)
                    {
                        $calendarEvent->set_byday(
                            $contentObjectClassName::get_byday_ical_format(
                                $values[$frequencyName][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_RANK],
                                $values[$frequencyName][$contentObjectClassName::PROPERTY_BYDAY][self::PARAM_DAY]
                            )
                        );

                        $calendarEvent->set_bymonth($values[$frequencyName][$contentObjectClassName::PROPERTY_BYMONTH]);
                        $calendarEvent->set_bymonthday(null);
                    }
                    else
                    {
                        $calendarEvent->set_bymonthday(
                            implode(',', $values[$frequencyName][$contentObjectClassName::PROPERTY_BYMONTHDAY])
                        );

                        $calendarEvent->set_bymonth($values[$frequencyName][$contentObjectClassName::PROPERTY_BYMONTH]);
                        $calendarEvent->set_byday(null);
                    }

                    $calendarEvent->set_frequency_interval(
                        $values[$frequencyName][$contentObjectClassName::PROPERTY_FREQUENCY_INTERVAL]
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
                    $calendarEvent->set_frequency_count($values[$contentObjectClassName::PROPERTY_FREQUENCY_COUNT]);
                    $calendarEvent->set_until(0);
                    break;
                case 3 :
                    $calendarEvent->set_frequency_count(0);
                    $calendarEvent->set_until(
                        DatetimeUtilities::getInstance()->timeFromDatepicker(
                            $values[$contentObjectClassName::PROPERTY_UNTIL]
                        )
                    );
            }
        }
    }
}
