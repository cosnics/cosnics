<?php
namespace Chamilo\Core\Tracking\Storage;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\Tracking\Storage\DataClass\EventRelTracker;
use Chamilo\Core\Tracking\Storage\DataClass\TrackerRegistration;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: tracking_data_manager.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * 
 * @package tracking.lib
 */

/**
 * This is a skeleton for a data manager for tracking manager Data managers must extend this class and implement its
 * abstract methods.
 * If the user configuration dictates that the "database" data manager is to be used, this class will
 * automatically attempt to instantiate "DatabaseDataManager"; hence, this naming convention must be respected for all
 * extensions of this class.
 * 
 * @author Sven Vanpoucke
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'tracking_';

    /**
     * Retrieves the event with the given name
     * 
     * @param $name String
     * @return Event event
     */
    public static function retrieve_event_by_name($event_name, $block = null)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Event :: class_name(), Event :: PROPERTY_NAME), 
            new StaticConditionVariable($event_name));
        
        if ($block)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Event :: class_name(), Event :: PROPERTY_CONTEXT), 
                new StaticConditionVariable($block));
        }
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(Event :: class_name(), new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieve all trackers from an event
     * 
     * @param $event_id int
     * @param $active Bool true if only the active ones should be shown (default true)
     * @return array of Tracker Registrations
     */
    public static function retrieve_trackers_from_event($event_id, $active = true)
    {
        $joins = array();
        $joins[] = new Join(
            EventRelTracker :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(EventRelTracker :: class_name(), EventRelTracker :: PROPERTY_TRACKER_ID), 
                new PropertyConditionVariable(TrackerRegistration :: class_name(), TrackerRegistration :: PROPERTY_ID)), 
            Join :: TYPE_LEFT);
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(EventRelTracker :: class_name(), EventRelTracker :: PROPERTY_EVENT_ID), 
            new StaticConditionVariable($event_id));
        
        if ($active)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(EventRelTracker :: class_name(), EventRelTracker :: PROPERTY_ACTIVE), 
                new StaticConditionVariable(1));
        }
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieves(
            TrackerRegistration :: class_name(), 
            new DataClassRetrievesParameters($condition, null, null, array(), new Joins($joins)))->as_array();
    }

    /**
     * Retrieves an event tracker relation by given id's
     * 
     * @param $event_id int the event id
     * @param $tracker_id int the tracker id
     * @return EventTrackerRelation that belongs to the given id's
     */
    public static function retrieve_event_tracker_relation($event_id, $tracker_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(EventRelTracker :: class_name(), Event :: PROPERTY_TRACKER_ID), 
            new StaticConditionVariable($tracker_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(EventRelTracker :: class_name(), Event :: PROPERTY_EVENT_ID), 
            new StaticConditionVariable($event_id));
        $condition = new AndCondition($conditions);
        
        return self :: retrieve(EventRelTracker :: class_name(), new DataClassRetrieveParameters($condition));
    }
}
