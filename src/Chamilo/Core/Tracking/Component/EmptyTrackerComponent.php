<?php
namespace Chamilo\Core\Tracking\Component;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: emptytracker.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Component to empty a tracker
 */
class EmptyTrackerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $tracker_ids = Request :: get(self :: PARAM_TRACKER_ID);
        $event_ids = Request :: get(self :: PARAM_EVENT_ID);
        $type = Request :: get(self :: PARAM_TYPE);

        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        if (($type == 'event' && $event_ids) || ($type == 'tracker' && $event_ids && $tracker_ids) || ($type == 'all'))
        {
            switch ($type)
            {
                case 'event' :
                    $this->empty_events($event_ids);
                    break;
                case 'tracker' :
                    $this->empty_trackers($event_ids, $tracker_ids);
                    break;
                case 'all' :
                    $this->empty_all_events();
                    break;
            }
        }
        else
        {
            return $this->display_error_page(Translation :: get("NoObjectSelected"));
        }
    }

    /**
     * Empty the chosen trackers for a given event
     *
     * @param $event_id int the chosen event
     * @param int array $tracker_ids array of chosen trackers
     */
    public function empty_trackers($event_id, $tracker_ids)
    {
        if (! is_array($tracker_ids))
        {
            $tracker_ids = array($tracker_ids);
        }

        $event = $this->retrieve_event($event_id);

        $success = true;

        foreach ($tracker_ids as $tracker_id)
        {
            $trackerregistration = $this->retrieve_tracker_registration($tracker_id);
            $tracker = Tracker :: factory($trackerregistration->get_tracker(), $trackerregistration->get_context());

            if (! $tracker->empty_tracker($event))
            {
                $success = false;
            }
        }

        $this->redirect(
            Translation :: get($success ? 'TrackerEmpty' : 'TrackerNotEmpty'),
            ($success ? false : true),
            array(Application :: PARAM_ACTION => self :: ACTION_VIEW_EVENT, self :: PARAM_EVENT_ID => $event_id));
    }

    /**
     * Empty the chosen trackers for a given events
     *
     * @param int array $event_ids the chosen events
     */
    public function empty_events($event_ids)
    {
        if (! is_array($event_ids))
        {
            $event_ids = array($event_ids);
        }

        $success = true;

        foreach ($event_ids as $event_id)
        {
            $event = $this->retrieve_event($event_id);
            if (! $this->empty_trackers_for_event($event))
            {
                $success = false;
            }
        }

        $this->redirect(
            Translation :: get($success ? 'TrackerEmpty' : 'TrackerNotEmpty'),
            ($success ? false : true),
            array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS));
    }

    /**
     * auxiliary function for to clear all trackers for an event
     *
     * @param $event Event
     */
    public function empty_trackers_for_event($event)
    {
        $trackerregistrations = $this->retrieve_trackers_from_event($event->get_id());

        foreach ($trackerregistrations as $trackerregistration)
        {
            $classname = $trackerregistration->get_context() . '\\' .
                 (string) StringUtilities :: getInstance()->createString($trackerregistration->get_tracker())->upperCamelize();
            $tracker = new $classname();
            if (! $tracker->remove())
                return false;
        }

        return true;
    }

    /**
     * Empty all events
     */
    public function empty_all_events()
    {
        $events = $this->retrieve_events();
        $success = true;

        foreach ($events as $event)
        {
            if (! $this->empty_trackers_for_event($event))
                $success = false;

            $this->redirect(
                Translation :: get($success ? 'TrackerEmpty' : 'TrackerNotEmpty'),
                ($success ? false : true),
                array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('tracking_empty_tracker');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_EVENT_ID, self :: PARAM_TRACKER_ID, self :: PARAM_TYPE);
    }
}
