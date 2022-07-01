<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Form;

use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Libraries\Calendar\Form\RecurringContentObjectForm;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\Task\Form
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TaskForm extends RecurringContentObjectForm
{

    /**
     * @throws \Exception
     */
    public function addTaskPropertiesToForm()
    {
        $translator = $this->getTranslator();

        $this->addElement(
            'category', $translator->trans('Properties', [], 'Chamilo\Core\Repository\ContentObject\Task')
        );

        $this->addElement(
            'select', Task::PROPERTY_PRIORITY,
            $translator->trans('Priority', [], 'Chamilo\Core\Repository\ContentObject\Task'),
            Task::get_priority_options(), array('class' => 'form-control')
        );

        $this->addElement(
            'select', Task::PROPERTY_CATEGORY,
            $translator->trans('TaskType', [], 'Chamilo\Core\Repository\ContentObject\Task'),
            Task::get_types_options(), array('class' => 'form-control')
        );

        $this->add_datepicker(
            Task::PROPERTY_START_DATE,
            $translator->trans('StartDate', [], 'Chamilo\Core\Repository\ContentObject\Task'), true
        );
        $this->add_datepicker(
            Task::PROPERTY_DUE_DATE,
            $translator->trans('EndDate', [], 'Chamilo\Core\Repository\ContentObject\Task'), true
        );

        $this->addFrequencyPropertiesToForm();
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
        $this->addTaskPropertiesToForm();
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
        $this->addTaskPropertiesToForm();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function create_content_object()
    {
        $task = new Task();

        $this->setTaskProperties($task);
        $this->setRecurrenceProperties($task);

        $this->set_content_object($task);

        return parent::create_content_object();
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
         * @var \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task $task
         */
        $task = $this->get_content_object();

        if (isset($task) && $this->form_type == self::TYPE_EDIT)
        {
            $defaults[Task::PROPERTY_CATEGORY] = $task->get_category();
            $defaults[Task::PROPERTY_PRIORITY] = $task->get_priority();

            $defaults[Task::PROPERTY_START_DATE] = $task->get_start_date();
            $defaults[Task::PROPERTY_DUE_DATE] = $task->get_due_date();
        }
        else
        {
            $defaults[Task::PROPERTY_START_DATE] = time();
            $defaults[Task::PROPERTY_DUE_DATE] = time() + 3600;
        }

        parent::setDefaults($defaults);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task $task
     */
    public function setTaskProperties($task)
    {
        $values = $this->exportValues();

        $task->set_category($values[Task::PROPERTY_CATEGORY]);
        $task->set_priority($values[Task::PROPERTY_PRIORITY]);

        $task->set_start_date(DatetimeUtilities::getInstance()->timeFromDatepicker($values[Task::PROPERTY_START_DATE]));
        $task->set_due_date(DatetimeUtilities::getInstance()->timeFromDatepicker($values[Task::PROPERTY_DUE_DATE]));
    }

    /**
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function update_content_object()
    {
        /**
         * @var \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task $task
         */
        $task = $this->get_content_object();

        $this->setTaskProperties($task);
        $this->setRecurrenceProperties($task);

        return parent::update_content_object();
    }
}
