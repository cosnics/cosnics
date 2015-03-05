<?php
namespace Chamilo\Core\Tracking;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\Tracking\Storage\DataClass\TrackerRegistration;
use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * $Id: tracking_manager.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager
 */

/**
 * A tracking manager provides some functionalities to the admin to manage his trackers and events.
 * For each
 * functionality a component is available.
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'tracking';
    const PARAM_EVENT_ID = 'event_id';
    const PARAM_TRACKER_ID = 'track_id';
    const PARAM_REF_ID = 'ref_id';
    const PARAM_TYPE = 'type';
    const PARAM_EXTRA = 'extra';
    const ACTION_BROWSE_EVENTS = 'admin_event_browser';
    const ACTION_VIEW_EVENT = 'admin_event_viewer';
    const ACTION_CHANGE_ACTIVE = 'activity_changer';
    const ACTION_ACTIVATE_EVENT = 'event_activator';
    const ACTION_DEACTIVATE_EVENT = 'event_deactivator';
    const ACTION_EMPTY_TRACKER = 'empty_tracker';
    const ACTION_EMPTY_EVENT_TRACKERS = 'empty_event_tracker';
    const ACTION_ARCHIVE = 'archiver';
    const DEFAULT_ACTION = self :: ACTION_BROWSE_EVENTS;

    private $tdm;

    private function get_tracking_data_manager()
    {
        if ($this->tdm == null)
        {
            $this->tdm = $this->tdm = DataManager :: get_instance();
        }
        return $this->tdm;
    }

    public function set_tracking_data_manager(DataManager $tdm)
    {
        $this->tdm = $tdm;
    }

    /**
     * Gets the url for the event browser
     *
     * @return String URL for event browser
     */
    public function get_browser_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS));
    }

    /**
     * Retrieves the change active url
     *
     * @param string $type event or tracker
     * @param Object $object Event or Tracker Object
     * @return the change active component url
     */
    public function get_change_active_url($type, $event_id, $tracker_id = null)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_ACTIVE;
        $parameters[self :: PARAM_TYPE] = $type;
        $parameters[self :: PARAM_EVENT_ID] = $event_id;
        if ($tracker_id)
            $parameters[self :: PARAM_TRACKER_ID] = $tracker_id;

        return $this->get_url($parameters);
    }

    /**
     * Retrieves the event viewer url
     *
     * @param Event $event
     * @return the event viewer url for the given event
     */
    public function get_event_viewer_url($event)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_VIEW_EVENT, self :: PARAM_EVENT_ID => $event->get_id()));
    }

    /**
     * Retrieves the empty tracker url
     *
     * @see TrackingManager :: get_empty_tracker_url()
     */
    public function get_empty_tracker_url($type, $event_id, $tracker_id = null)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EMPTY_TRACKER,
                self :: PARAM_EVENT_ID => $event_id,
                self :: PARAM_TRACKER_ID => $tracker_id,
                self :: PARAM_TYPE => $type));
    }

    /**
     * Retrieves the events
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param String $order_property
     */
    public function retrieve_events($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return DataManager :: retrieves(
            Event :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     * Count the events from a given condition
     *
     * @param Condition $conditions
     */
    public function count_events($conditions = null)
    {
        return DataManager :: count(Event :: class_name(), new DataClassCountParameters($conditions));
    }

    /**
     * Retrieves an event by the given id
     *
     * @param int $event_id
     * @return Event event
     */
    public function retrieve_event($event_id)
    {
        return DataManager :: retrieve_by_id(Event :: class_name(), $event_id);
    }

    /**
     * Retrieves the trackers from a given event
     *
     * @param int $event_id the event id
     * @return array of trackers
     */
    public function retrieve_trackers_from_event($event_id)
    {
        return DataManager :: retrieve_trackers_from_event($event_id, false);
    }

    /**
     * Retrieves the event tracker relation by given id's
     *
     * @param int $event_id the event id
     * @param int $tracker_id the tracker id
     * @return EventTrackerRelation
     */
    public function retrieve_event_tracker_relation($event_id, $tracker_id)
    {
        return DataManager :: retrieve_event_tracker_relation($event_id, $tracker_id);
    }

    /**
     * Retrieves the tracker for the given id
     *
     * @param int $tracker_id the given tracker id
     * @return TrackerRegistration the tracker registration
     */
    public function retrieve_tracker_registration($tracker_id)
    {
        return DataManager :: retrieve_by_id(TrackerRegistration :: class_name(), $tracker_id);
    }

    /**
     * Retrieves an event by name
     *
     * @param string $eventname
     * @return Event event
     */
    public function retrieve_event_by_name($eventname)
    {
        return DataManager :: retrieve_event_by_name($eventname);
    }
}
