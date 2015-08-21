<?php
namespace Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Application\Calendar\Extension\SyllabusPlus\Storage\DataManager;
use Chamilo\Application\Calendar\Extension\SyllabusPlus\Storage\ResultSet;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CalendarRepository
{

    /**
     *
     * @var \Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository
     */
    private static $instance;

    /**
     *
     * @return \Chamilo\Application\Calendar\Extension\SyllabusPlus\Repository\CalendarRepository
     */
    static public function getInstance()
    {
        if (is_null(static :: $instance))
        {
            self :: $instance = new static();
        }

        return static :: $instance;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $fromDate
     * @param integer $toDate
     * @return \Chamilo\Libraries\Storage\ResultSet\ArrayResultSet
     */
    public function findEventsForUserAndBetweenDates(User $user, $fromDate, $toDate)
    {
        if ($user->get_official_code())
        {
            $query = 'SELECT * FROM [dbo].[v_syllabus_courses] WHERE person_id = N\'' . $user->get_official_code() . '\'';
            $statement = DataManager :: get_instance()->get_connection()->query($query);
            return new ResultSet($statement);
        }
        else
        {
            return new ArrayResultSet(array());
        }
    }

    public function findWeekLabels()
    {
        $query = 'SELECT * FROM [dbo].[v_syllabus_weeks]';
        $statement = DataManager :: get_instance()->get_connection()->query($query);
        return new ResultSet($statement);
    }
}