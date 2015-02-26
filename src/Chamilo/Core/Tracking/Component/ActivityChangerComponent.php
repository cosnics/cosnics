<?php
namespace Chamilo\Core\Tracking\Component;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: activity_changer.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Component for change of activity
 */
class ActivityChangerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $tracker_ids = Request :: get(self :: PARAM_TRACKER_ID);
        $type = Request :: get(self :: PARAM_TYPE);
        $event_ids = Request :: get(self :: PARAM_EVENT_ID);

        if (! $this->get_user() || ! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        if (($type == 'event' && $event_ids) || ($type == 'tracker' && $event_ids && $tracker_ids) || ($type == 'all'))
        {
            switch ($type)
            {
                case 'event' :
                    $this->change_event_activity($event_ids);
                    break;
                case 'tracker' :
                    $this->change_tracker_activity($event_ids, $tracker_ids);
                    break;
                case 'all' :
                    $this->change_tracking_activity();
                    break;
            }
        }
        else
        {
            return $this->display_error_page(Translation :: get("NoObjectSelected"));
        }
    }

    /**
     * Function to change the activity of events
     *
     * @param Array of event ids
     */
    public function change_event_activity($event_ids)
    {
        if ($event_ids)
        {
            if (! is_array($event_ids))
            {
                $event_ids = array($event_ids);
            }

            $success = true;

            foreach ($event_ids as $event_id)
            {
                $event = $this->retrieve_event($event_id);
                if (Request :: get('extra'))
                {
                    $event->set_active(Request :: get('extra') == 'enable' ? 1 : 0);
                }
                else
                    $event->set_active(! $event->get_active());

                if (! $event->update())
                    $success = false;
            }

            $this->redirect(
                Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'),
                ($success ? false : true),
                array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS));
        }
    }

    /**
     * Function to change the activity of trackers
     *
     * @param int event_id the event_id
     * @param array of tracker ids
     */
    public function change_tracker_activity($event_id, $tracker_ids)
    {
        if ($tracker_ids)
        {
            if (! is_array($tracker_ids))
            {
                $tracker_ids = array($tracker_ids);
            }

            $success = true;

            foreach ($tracker_ids as $tracker_id)
            {
                $relation = $this->retrieve_event_tracker_relation($event_id, $tracker_id);

                if (Request :: get('extra'))
                {
                    $relation->set_active(Request :: get('extra') == 'enable' ? 1 : 0);
                }
                else
                    $relation->set_active(! $relation->get_active());

                if (! $relation->update())
                    $success = false;
            }

            $this->redirect(
                Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'),
                ($success ? false : true),
                array(Application :: PARAM_ACTION => self :: ACTION_VIEW_EVENT, self :: PARAM_EVENT_ID => $event_id));
        }
    }

    /**
     * Enables / Disables all events and trackers
     */
    public function change_tracking_activity()
    {
        $setting = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_from_variable_name(
            'enable_tracking',
            'core\tracking');
        $setting->set_value($setting->get_value() == 1 ? 0 : 1);
        $success = $setting->update();

        $this->redirect(
            Translation :: get($success ? 'ActivityUpdated' : 'ActivityNotUpdated'),
            ($success ? false : true),
            array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_EVENTS));
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('tracking_activity_changer');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_EVENT_ID, self :: PARAM_TRACKER_ID, self :: PARAM_TYPE);
    }
}
