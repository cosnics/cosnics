<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Form;

use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * $Id: task_form.class.php
 *
 * @package repository.lib.content_object.task
 */

/**
 * This class represents a form to create or update task
 */
class TaskForm extends ContentObjectForm
{
    const PARAM_RANGE = 'range';
    const PARAM_FREQUENCY_RANGE = 'frequency_range';
    const PARAM_DAILY = 'daily';
    const PARAM_WEEKLY = 'weekly';
    const PARAM_MONTHLY = 'monthly';
    const PARAM_YEARLY = 'yearly';
    const PARAM_OPTION = 'option';
    const PARAM_RANK = 'rank';
    const PARAM_DAY = 'day';
    const PARAM_PRIORITY = 'priority';
    const PARAM_TYPE = 'type';

    // Inherited
    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->add_calendar_form();
    }

    // Inherited
    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->add_calendar_form();
    }

    public function add_calendar_form()
    {
        $this->addElement('category', Translation :: get('Properties'));

        $options_priority = Task :: get_priority_options();
        $choices_priority = array();
        $choices_priority[] = $this->createElement('select', Task :: PROPERTY_PRIORITY, null, $options_priority);
        $this->addGroup($choices_priority, self :: PARAM_PRIORITY, Translation :: get('Priority'), '', false);

        $options_type = Task :: get_types_options();
        $choices_type = array();
        $choices_type[] = $this->createElement('select', Task :: PROPERTY_CATEGORY, null, $options_type);
        $this->addGroup($choices_type, self :: PARAM_TYPE, Translation :: get('TaskType'), '', false);

        $start_date = array();
        $start_date[] = $this->createElement(
            'text',
            Task :: PROPERTY_START_DATE,
            null,
            'id="start_date" style="width:120px;"');
        $this->addGroup($start_date, Task :: PROPERTY_START_DATE, Translation :: get('StartDate'), '', false);
        $this->get_renderer()->setGroupElementTemplate('{element}', Task :: PROPERTY_START_DATE);

        $due_date = array();
        $due_date[] = $this->createElement(
            'text',
            Task :: PROPERTY_DUE_DATE,
            null,
            'id="due_date" style="width:120px;"');
        $this->addGroup($due_date, Task :: PROPERTY_DUE_DATE, Translation :: get('EndDate'), '', false);
        $this->get_renderer()->setGroupElementTemplate('{element}', Task :: PROPERTY_DUE_DATE);

        // frequency
        // no repeat
        $this->addElement(
            'radio',
            Task :: PROPERTY_FREQUENCY,
            Translation :: get('Frequency'),
            Translation :: get('NoRepeat'),
            0);

        // daily
        $this->addElement('radio', Task :: PROPERTY_FREQUENCY, '', Translation :: get('Daily'), 1);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_1" class="frequency">');
        $daily_elements = array();
        $daily_elements[] = $this->createElement('static', '', null, Translation :: get('Every'));

        $daily_elements[] = $this->createElement(
            'text',
            Task :: PROPERTY_FREQUENCY_INTERVAL,
            '',
            array('style' => 'width:50px', 'maxlength' => 2));
        $daily_elements[] = $this->createElement('static', '', null, Translation :: get('Days'));
        $this->addGroup($daily_elements, self :: PARAM_DAILY);

        $this->addElement('html', '</div>');

        // weekly
        $this->addElement('radio', Task :: PROPERTY_FREQUENCY, '', Translation :: get('Weekly'), 2);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_2" class="frequency">');

        $weekly_elements = array();
        $weekly_elements[] = $this->createElement('static', '', null, Translation :: get('Every'));
        $weekly_elements[] = $this->createElement(
            'text',
            Task :: PROPERTY_FREQUENCY_INTERVAL,
            '',
            array('style' => 'width:50px', 'maxlength' => 2));
        $weekly_elements[] = $this->createElement('static', '', null, Translation :: get('Weeks'));
        $this->addGroup($weekly_elements, self :: PARAM_WEEKLY);

        $this->addElement(
            'select',
            self :: PARAM_WEEKLY . '[' . Task :: PROPERTY_BYDAY . ']',
            '',
            Task :: get_byday_options(),
            'multiple="true"');
        $this->addElement('html', '</div>');

        //
        $this->addElement('radio', Task :: PROPERTY_FREQUENCY, '', Translation :: get('EveryWeekday'), 3);
        //
        $this->addElement('radio', Task :: PROPERTY_FREQUENCY, '', Translation :: get('BiWeekly'), 4);

        // monthly
        $this->addElement('radio', Task :: PROPERTY_FREQUENCY, '', Translation :: get('Monthly'), 5);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_5" class="frequency">');

        $monthly_elements = array();
        $monthly_elements[] = $this->createElement('static', '', null, Translation :: get('Every'));
        $monthly_elements[] = $this->createElement(
            'text',
            Task :: PROPERTY_FREQUENCY_INTERVAL,
            '',
            array('style' => 'width:50px', 'maxlength' => 2));
        $monthly_elements[] = $this->createElement('static', '', null, Translation :: get('Months'));
        $this->addGroup($monthly_elements, self :: PARAM_MONTHLY);

        $monthly_byday_elements = array();
        $monthly_byday_elements[] = $this->createElement(
            'radio',
            self :: PARAM_MONTHLY . '[' . self :: PARAM_OPTION . ']',
            '',
            '',
            0);
        $monthly_byday_elements[] = $this->createElement(
            'select',
            self :: PARAM_MONTHLY . '[' . Task :: PROPERTY_BYDAY . '][' . self :: PARAM_RANK . ']',
            '',
            Task :: get_rank_options());
        $monthly_byday_elements[] = $this->createElement(
            'select',
            self :: PARAM_MONTHLY . '[' . Task :: PROPERTY_BYDAY . '][' . self :: PARAM_DAY . ']',
            '',
            Task :: get_byday_options());
        $this->addGroup($monthly_byday_elements);

        $monthly_bymonthday_elements = array();
        $monthly_bymonthday_elements[] = $this->createElement('radio', self :: PARAM_OPTION, '', '', 1);
        $monthly_bymonthday_elements[] = $this->createElement('static', '', null, Translation :: get('RecurOnDays'));
        $monthly_bymonthday_elements[] = $this->createElement(
            'select',
            Task :: PROPERTY_BYMONTHDAY,
            '',
            Task :: get_bymonthday_options(),
            'multiple="true"');

        $this->addGroup($monthly_bymonthday_elements, self :: PARAM_MONTHLY);
        $this->addElement('html', '</div>');

        // yearly
        $this->addElement('radio', Task :: PROPERTY_FREQUENCY, '', Translation :: get('Yearly'), 6);
        $this->addElement('html', '<div style="padding-left:50px;" id="frequency_6" class="frequency">');

        $yearly_elements = array();
        $yearly_elements[] = $this->createElement('static', '', null, Translation :: get('Every'));
        $yearly_elements[] = $this->createElement(
            'text',
            Task :: PROPERTY_FREQUENCY_INTERVAL,
            '',
            array('style' => 'width:50px', 'maxlength' => 2));
        $yearly_elements[] = $this->createElement('static', '', null, Translation :: get('Years'));
        $this->addGroup($yearly_elements, self :: PARAM_YEARLY);

        $yearly_bymonthday_elements = array();
        $yearly_bymonthday_elements[] = $this->createElement('radio', self :: PARAM_OPTION, '', '', 0);
        $yearly_bymonthday_elements[] = $this->createElement('static', '', null, Translation :: get('Every'));
        $yearly_bymonthday_elements[] = $this->createElement(
            'select',
            Task :: PROPERTY_BYMONTHDAY,
            '',
            Task :: get_bymonthday_options(),
            'multiple="true"');
        $this->addGroup($yearly_bymonthday_elements, self :: PARAM_YEARLY);

        $yearly_byday_elements = array();
        $yearly_byday_elements[] = $this->createElement(
            'radio',
            self :: PARAM_YEARLY . '[' . self :: PARAM_OPTION . ']',
            '',
            '',
            1);
        $yearly_byday_elements[] = $this->createElement(
            'select',
            self :: PARAM_YEARLY . '[' . Task :: PROPERTY_BYDAY . ']' . '[' . self :: PARAM_RANK . ']',
            '',
            Task :: get_rank_options());
        $yearly_byday_elements[] = $this->createElement(
            'select',
            self :: PARAM_YEARLY . '[' . Task :: PROPERTY_BYDAY . ']' . '[' . self :: PARAM_DAY . ']',
            '',
            Task :: get_byday_options());
        $this->addGroup($yearly_byday_elements);

        $yearly_month = array();
        $yearly_month[] = $this->createElement('static', '', null, Translation :: get('Of'));
        $yearly_month[] = $this->createElement(
            'select',
            self :: PARAM_YEARLY . '[' . Task :: PROPERTY_BYMONTH . ']',
            '',
            Task :: get_bymonth_options());
        $this->addGroup($yearly_month);
        $this->addElement('html', '</div>');

        // range
        $this->addElement('html', '<div class="range">');
        $this->addElement('radio', self :: PARAM_RANGE, Translation :: get('Range'), Translation :: get('NoEndDate'), 1);

        $interval_elements = array();
        $interval_elements[] = $this->createElement('radio', self :: PARAM_RANGE, '', Translation :: get('Create'), 2);
        $interval_elements[] = $this->createElement(
            'text',
            Task :: PROPERTY_FREQUENCY_COUNT,
            '',
            array('style' => 'width:50px', 'maxlength' => 2));
        $interval_elements[] = $this->createElement('static', null, null, Translation :: get('Appointments'));
        $this->addGroup($interval_elements, '', '', null, false);

        $until_elements = array();
        $until_elements[] = $this->createElement('radio', self :: PARAM_RANGE, '', Translation :: get('Until'), 3);
        $until_elements[] = $this->createElement(
            'datepicker',
            Task :: PROPERTY_UNTIL,
            '',
            array('form_name' => $this->getAttribute('name'), 'class' => Task :: PROPERTY_UNTIL),
            true);
        $this->addGroup($until_elements, '', '', null, false);
        $this->addElement('html', '</div>');
        $this->addElement('category');
        $this->addElement(
            'html',
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\Task', true) . 'Dates.js'));
    }

    // Inherited
    public function setDefaults($defaults = array())
    {
        $task = $this->get_content_object();

        if (isset($task) && $this->form_type == self :: TYPE_EDIT)
        {
            $defaults[Task :: PROPERTY_CATEGORY] = $task->get_category();
            $defaults[Task :: PROPERTY_PRIORITY] = $task->get_priority();

            $defaults[Task :: PROPERTY_START_DATE] = DatetimeUtilities :: format_locale_date(
                '%d-%m-%Y  %H:%M',
                $task->get_start_date());
            $defaults[Task :: PROPERTY_DUE_DATE] = DatetimeUtilities :: format_locale_date(
                '%d-%m-%Y  %H:%M',
                $task->get_due_date());
            $defaults[Task :: PROPERTY_FREQUENCY] = $task->get_frequency();

            $repeats = $task->has_frequency();
            if ($repeats)
            {
                switch ($task->get_frequency())
                {
                    case 1 :
                        $defaults[self :: PARAM_DAILY][Task :: PROPERTY_FREQUENCY_INTERVAL] = $task->get_frequency_interval();
                        $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_BYDAY] = array(1);
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_MONTHLY][self :: PARAM_OPTION] = 0;
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYMONTHDAY] = array(1);
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_YEARLY][self :: PARAM_OPTION] = 0;
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYMONTHDAY] = array(1);
                        break;
                    case 2 :
                        if ($task->get_byday() == 'MO,TU,WE,TH,FR' && $task->get_frequency_interval() == 1)
                        {
                            $defaults[Task :: PROPERTY_FREQUENCY] = 3;
                            $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_BYDAY] = array(1);
                        }
                        elseif ($task->get_frequency_interval() == 2 && $task->get_byday() == '')
                        {
                            $defaults[Task :: PROPERTY_FREQUENCY] = 4;
                            $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                            $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_BYDAY] = array(1);
                        }
                        else
                        {
                            $bydays = Task :: get_byday_parts($task->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_BYDAY][] = Task :: get_day_format(
                                    $byday[1]);
                            }
                            $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = $task->get_frequency_interval();
                        }
                        $defaults[self :: PARAM_DAILY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_MONTHLY][self :: PARAM_OPTION] = 0;
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYMONTHDAY] = array(1);
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_YEARLY][self :: PARAM_OPTION] = 0;
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYMONTHDAY] = array(1);
                        break;

                    case 5 :
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = $task->get_frequency_interval();
                        if ($task->get_bymonthday())
                        {
                            $defaults[self :: PARAM_MONTHLY][self :: PARAM_OPTION] = 1;
                            $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYMONTHDAY] = $task->get_bymonthday();
                        }
                        else
                        {
                            $defaults[self :: PARAM_MONTHLY][self :: PARAM_OPTION] = 0;
                            $bydays = Task :: get_byday_parts($task->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYDAY][self :: PARAM_DAY] = Task :: get_day_format(
                                    $byday[1]);
                                $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = $byday[0];
                            }
                        }
                        $defaults[self :: PARAM_DAILY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_BYDAY] = array(1);
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_YEARLY][self :: PARAM_OPTION] = 0;
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYMONTHDAY] = array(1);
                        break;
                    case 6 :
                        $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = $task->get_frequency_interval();
                        if ($task->get_bymonthday())
                        {
                            $defaults[self :: PARAM_YEARLY][self :: PARAM_OPTION] = 0;
                            $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYMONTHDAY] = $task->get_bymonthday();
                            $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYMONTH] = $task->get_bymonth();
                        }
                        else
                        {
                            $defaults[self :: PARAM_YEARLY][self :: PARAM_OPTION] = 1;
                            $bydays = Task :: get_byday_parts($task->get_byday());
                            foreach ($bydays as $byday)
                            {
                                $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYDAY][self :: PARAM_DAY] = Task :: get_day_format(
                                    $byday[1]);
                                $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = $byday[0];
                                $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYMONTH] = $task->get_bymonth();
                            }
                        }
                        $defaults[self :: PARAM_DAILY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_BYDAY] = array(1);
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
                        $defaults[self :: PARAM_MONTHLY][self :: PARAM_OPTION] = 0;
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
                        $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYMONTHDAY] = array(1);
                        break;
                }

                if ($task->get_until() == 0)
                {
                    if ($task->get_frequency_count() > 0)
                    {
                        $defaults[Task :: PROPERTY_FREQUENCY_COUNT] = $task->get_frequency_count();
                        $defaults[self :: PARAM_RANGE] = 2;
                    }
                    else
                    {
                        $defaults[self :: PARAM_RANGE] = 1;
                        $defaults[Task :: PROPERTY_UNTIL] = 0;
                        $defaults[Task :: PROPERTY_FREQUENCY_COUNT] = 10;
                    }
                }
                else
                {
                    $defaults[self :: PARAM_RANGE] = 3;
                    $defaults[Task :: PROPERTY_UNTIL] = $task->get_until();
                    $defaults[Task :: PROPERTY_FREQUENCY_COUNT] = 10;
                }
            }
        }
        else
        {
            $defaults[Task :: PROPERTY_FREQUENCY] = 0;
            // $defaults[Task :: PROPERTY_START_DATE] = time();
            // $defaults[Task :: PROPERTY_DUE_DATE] = strtotime('+1 Hour', time());

            $defaults[self :: PARAM_DAILY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;

            $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self :: PARAM_WEEKLY][Task :: PROPERTY_BYDAY] = array(1);

            $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self :: PARAM_MONTHLY][self :: PARAM_OPTION] = 0;
            $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
            $defaults[self :: PARAM_MONTHLY][Task :: PROPERTY_BYMONTHDAY] = array(1);

            $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_FREQUENCY_INTERVAL] = 1;
            $defaults[self :: PARAM_YEARLY][self :: PARAM_OPTION] = 0;
            $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYDAY][self :: PARAM_RANK] = 0;
            $defaults[self :: PARAM_YEARLY][Task :: PROPERTY_BYMONTHDAY] = array(1);

            $defaults[self :: PARAM_RANGE] = 1;
            $defaults[Task :: PROPERTY_UNTIL] = null;
            $defaults[Task :: PROPERTY_FREQUENCY_COUNT] = 10;
        }

        parent :: setDefaults($defaults);
    }

    // Inherited
    public function create_content_object()
    {
        $object = new Task();
        $object = $this->configure_content_object($object);

        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    // Inherited
    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object = $this->configure_content_object($object);

        return parent :: update_content_object();
    }

    public function configure_content_object($object)
    {
        $values = $this->exportValues();

        $object->set_category($values[Task :: PROPERTY_CATEGORY]);
        $object->set_priority($values[Task :: PROPERTY_PRIORITY]);

        $object->set_start_date(strtotime($values[Task :: PROPERTY_START_DATE]));
        $object->set_due_date(strtotime($values[Task :: PROPERTY_DUE_DATE]));
        $frequency = $values[Task :: PROPERTY_FREQUENCY];

        $object->set_frequency($values[Task :: PROPERTY_FREQUENCY]);

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
                $frequency_type = self :: PARAM_DAILY;
                $object->set_byday(null);
                $object->set_bymonthday(null);
                $object->set_bymonth(null);
                break;
            case 2 :
                $frequency_type = self :: PARAM_WEEKLY;
                $bydays = array();
                foreach ($values[$frequency_type][Task :: PROPERTY_BYDAY] as $byday)
                {
                    $bydays[] = Task :: get_day_ical_format($byday);
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
                $frequency_type = self :: PARAM_MONTHLY;
                if ($values[$frequency_type][self :: PARAM_OPTION] == 0)
                {
                    $object->set_byday(
                        Task :: get_byday_ical_format(
                            $values[$frequency_type][Task :: PROPERTY_BYDAY][self :: PARAM_RANK],
                            $values[$frequency_type][Task :: PROPERTY_BYDAY][self :: PARAM_DAY]));

                    $object->set_bymonthday(null);
                }
                else
                {
                    $object->set_bymonthday(implode(',', $values[$frequency_type][Task :: PROPERTY_BYMONTHDAY]));
                    $object->set_byday(null);
                }
                $object->set_bymonth(null);
                break;
            case 6 :
                $frequency_type = self :: PARAM_YEARLY;

                if ($values[$frequency_type][self :: PARAM_OPTION] == 0)
                {
                    $object->set_bymonthday(implode(',', $values[$frequency_type][Task :: PROPERTY_BYMONTHDAY]));
                    $object->set_bymonth($values[$frequency_type][Task :: PROPERTY_BYMONTH]);
                    $object->set_byday(null);
                }
                else
                {
                    $object->set_byday(
                        Task :: get_byday_ical_format(
                            $values[$frequency_type][Task :: PROPERTY_BYDAY][self :: PARAM_RANK],
                            $values[$frequency_type][Task :: PROPERTY_BYDAY][self :: PARAM_DAY]));
                    $object->set_bymonth($values[$frequency_type][Task :: PROPERTY_BYMONTH]);
                    $object->set_bymonthday(null);
                }

                break;
        }

        if (in_array($frequency, array(1, 2, 5, 6)))
        {
            $object->set_frequency_interval($values[$frequency_type][Task :: PROPERTY_FREQUENCY_INTERVAL]);
        }

        switch ($values[self :: PARAM_RANGE])
        {
            case 1 :
                $object->set_until(0);
                $object->set_frequency_count(0);
                break;
            case 2 :
                $object->set_frequency_count($values[Task :: PROPERTY_FREQUENCY_COUNT]);
                $object->set_until(0);
                break;
            case 3 :
                $object->set_frequency_count(0);
                $object->set_until(DatetimeUtilities :: time_from_datepicker($values[Task :: PROPERTY_UNTIL]));
        }

        return $object;
    }
}
