<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Libraries\Calendar\Event\RecurrenceRules;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Task\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RecurrenceRulesParser
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task
     */
    private $task;

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }

    public function getRules()
    {
        $task = $this->getTask();
        
        $byDay = $task->get_byday() ? explode(',', $task->get_byday()) : [];
        $byMonthDay = $task->get_bymonthday() ? explode(',', $task->get_bymonthday()) : [];
        $byMonth = $task->get_bymonth() ? explode(',', $task->get_bymonth()) : [];
        
        return new RecurrenceRules(
            $task->get_frequency(), 
            $task->get_until(), 
            $task->get_frequency_count(), 
            $task->get_frequency_interval(), 
            $byDay, 
            $byMonthDay, 
            $byMonth);
    }
}